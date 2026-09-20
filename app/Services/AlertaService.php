<?php

/**
 * Verifica uma leitura de sensor contra os limites configurados pela lavoura.
 *
 * Um alerta representa um episódio, não uma leitura: enquanto o parâmetro
 * continuar fora da faixa, o mesmo alerta acumula ocorrências. Antes era uma
 * linha (e um e-mail) por leitura — com 48 leituras por dia em cada sensor,
 * bastava um parâmetro descalibrado para enterrar o produtor em avisos.
 */

namespace App\Services;

use App\Mail\AlertaGeradoMail;
use App\Models\Alerta;
use App\Models\ConfiguracaoLimite;
use App\Models\LeituraSensor;
use App\Models\Sensor;
use Illuminate\Support\Facades\Mail;

class AlertaService
{
    /** Desvio (em relação à largura da faixa ideal) a partir do qual o alerta é crítico. */
    private const DESVIO_CRITICO = 0.3;

    /**
     * Histerese: para encerrar o episódio o valor precisa voltar para dentro
     * da faixa com esta folga (fração da largura). Sem isso, um valor oscilando
     * em cima do limite abriria e fecharia episódio — e mandaria e-mail — a
     * cada leitura.
     */
    private const FOLGA_NORMALIZACAO = 0.05;

    public function verificar(LeituraSensor $leitura): ?Alerta
    {
        $sensor = $leitura->sensor;

        if (!$sensor || !$sensor->id_lavoura || !$sensor->tp_sensor) {
            return null;
        }

        $limite = ConfiguracaoLimite::where('id_lavoura', $sensor->id_lavoura)
            ->where('tp_sensor', $sensor->tp_sensor->value)
            ->first();

        if (!$limite) {
            return null;
        }

        $direcao = $this->direcao($leitura->valor, $limite);

        if ($direcao === null) {
            $this->encerrarEpisodios($sensor, $leitura, $limite);

            return null;
        }

        $aberto = $this->episodioAberto($sensor, $direcao);

        return $aberto
            ? $this->acumular($aberto, $leitura, $limite, $direcao)
            : $this->abrir($sensor, $leitura, $limite, $direcao);
    }

    /** 'abaixo', 'acima' ou null quando a leitura está dentro da faixa. */
    private function direcao(float $valor, ConfiguracaoLimite $limite): ?string
    {
        if ($limite->valor_min !== null && $valor < $limite->valor_min) {
            return 'abaixo';
        }

        if ($limite->valor_max !== null && $valor > $limite->valor_max) {
            return 'acima';
        }

        return null;
    }

    private function episodioAberto(Sensor $sensor, string $direcao): ?Alerta
    {
        return Alerta::where('id_sensor', $sensor->id_sensor)
            ->where('tp_direcao', $direcao)
            ->whereNull('dt_normalizado')
            ->latest('dt_alerta')
            ->first();
    }

    private function abrir(Sensor $sensor, LeituraSensor $leitura, ConfiguracaoLimite $limite, string $direcao): Alerta
    {
        $alerta = Alerta::create([
            'id_sensor' => $sensor->id_sensor,
            'id_lavoura' => $sensor->id_lavoura,
            'tp_direcao' => $direcao,
            'tp_severidade' => $this->severidade($leitura->valor, $limite),
            'nu_ocorrencias' => 1,
            'ds_mensagem' => $this->mensagem($sensor, $leitura->valor, $limite, $direcao),
            'fl_lida' => false,
            'dt_alerta' => $leitura->dt_leitura,
            'dt_ultima_ocorrencia' => $leitura->dt_leitura,
        ]);

        // E-mail só na abertura do episódio, nunca nas repetições.
        $this->notificarPorEmail($alerta);

        return $alerta;
    }

    /**
     * Mesma anomalia continuando: atualiza a contagem e o texto para o valor
     * atual. A severidade só sobe — um episódio que já foi crítico não vira
     * "atenção" porque a leitura seguinte melhorou um pouco.
     */
    private function acumular(Alerta $alerta, LeituraSensor $leitura, ConfiguracaoLimite $limite, string $direcao): Alerta
    {
        $severidade = $this->severidade($leitura->valor, $limite);

        $alerta->update([
            'nu_ocorrencias' => $alerta->nu_ocorrencias + 1,
            'dt_ultima_ocorrencia' => $leitura->dt_leitura,
            'ds_mensagem' => $this->mensagem($alerta->sensor, $leitura->valor, $limite, $direcao),
            'tp_severidade' => $alerta->tp_severidade === 'critical' ? 'critical' : $severidade,
        ]);

        return $alerta;
    }

    /**
     * Parâmetro de volta à faixa (com folga): fecha os episódios abertos do
     * sensor, para que um desvio futuro comece um alerta novo.
     */
    private function encerrarEpisodios(Sensor $sensor, LeituraSensor $leitura, ConfiguracaoLimite $limite): void
    {
        if (!$this->normalizado($leitura->valor, $limite)) {
            return;
        }

        Alerta::where('id_sensor', $sensor->id_sensor)
            ->whereNull('dt_normalizado')
            ->update(['dt_normalizado' => $leitura->dt_leitura]);
    }

    private function normalizado(float $valor, ConfiguracaoLimite $limite): bool
    {
        $folga = $this->larguraDaFaixa($limite) * self::FOLGA_NORMALIZACAO;

        if ($limite->valor_min !== null && $valor < $limite->valor_min + $folga) {
            return false;
        }

        if ($limite->valor_max !== null && $valor > $limite->valor_max - $folga) {
            return false;
        }

        return true;
    }

    private function mensagem(Sensor $sensor, float $valor, ConfiguracaoLimite $limite, string $direcao): string
    {
        $unidade = $sensor->tp_sensor->unidade();
        $tipoLabel = $sensor->tp_sensor->label();

        return $direcao === 'abaixo'
            ? "{$tipoLabel} abaixo do limite mínimo em \"{$sensor->ds_nome}\" ({$valor}{$unidade}, mínimo {$limite->valor_min}{$unidade})"
            : "{$tipoLabel} acima do limite máximo em \"{$sensor->ds_nome}\" ({$valor}{$unidade}, máximo {$limite->valor_max}{$unidade})";
    }

    /**
     * Quão grave é o desvio: um valor que passou um pouco do limite não é a
     * mesma coisa que um que passou longe. A referência é a largura da faixa
     * ideal; quando só um dos lados está configurado, usa-se 20% do próprio
     * limite como referência.
     */
    private function severidade(float $valor, ConfiguracaoLimite $limite): string
    {
        $min = $limite->valor_min;
        $max = $limite->valor_max;

        $excedente = $min !== null && $valor < $min ? $min - $valor : $valor - $max;
        $referencia = $this->larguraDaFaixa($limite);

        if ($referencia <= 0) {
            return 'warning';
        }

        return $excedente / $referencia >= self::DESVIO_CRITICO ? 'critical' : 'warning';
    }

    private function larguraDaFaixa(ConfiguracaoLimite $limite): float
    {
        $min = $limite->valor_min;
        $max = $limite->valor_max;

        return $min !== null && $max !== null
            ? $max - $min
            : abs($min ?? $max) * 0.2;
    }

    private function notificarPorEmail(Alerta $alerta): void
    {
        $usuario = $alerta->sensor->propriedade?->usuario;

        if ($usuario && $usuario->fl_notificar_email_alerta && $usuario->email) {
            Mail::to($usuario->email)->send(new AlertaGeradoMail($alerta));
        }
    }
}
