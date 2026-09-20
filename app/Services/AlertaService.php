<?php

/**
 * Verifica uma leitura de sensor contra os limites configurados pela lavoura
 * e gera um alerta quando o valor estiver fora do intervalo permitido.
 */

namespace App\Services;

use App\Mail\AlertaGeradoMail;
use App\Models\Alerta;
use App\Models\ConfiguracaoLimite;
use App\Models\LeituraSensor;
use Illuminate\Support\Facades\Mail;

class AlertaService
{
    /** Desvio (em relação à largura da faixa ideal) a partir do qual o alerta é crítico. */
    private const DESVIO_CRITICO = 0.3;

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

        $unidade = $sensor->tp_sensor->unidade();
        $abaixoDoMinimo = $limite->valor_min !== null && $leitura->valor < $limite->valor_min;
        $acimaDoMaximo = $limite->valor_max !== null && $leitura->valor > $limite->valor_max;

        if (!$abaixoDoMinimo && !$acimaDoMaximo) {
            return null;
        }

        $tipoLabel = $sensor->tp_sensor->label();
        $mensagem = $abaixoDoMinimo
            ? "{$tipoLabel} abaixo do limite mínimo em \"{$sensor->ds_nome}\" ({$leitura->valor}{$unidade}, mínimo {$limite->valor_min}{$unidade})"
            : "{$tipoLabel} acima do limite máximo em \"{$sensor->ds_nome}\" ({$leitura->valor}{$unidade}, máximo {$limite->valor_max}{$unidade})";

        $alerta = Alerta::create([
            'id_sensor' => $sensor->id_sensor,
            'id_lavoura' => $sensor->id_lavoura,
            'tp_severidade' => $this->severidade($leitura->valor, $limite),
            'ds_mensagem' => $mensagem,
            'fl_lida' => false,
            'dt_alerta' => $leitura->dt_leitura,
        ]);

        $this->notificarPorEmail($alerta);

        return $alerta;
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
        $referencia = $min !== null && $max !== null
            ? $max - $min
            : abs($min ?? $max) * 0.2;

        if ($referencia <= 0) {
            return 'warning';
        }

        return $excedente / $referencia >= self::DESVIO_CRITICO ? 'critical' : 'warning';
    }

    private function notificarPorEmail(Alerta $alerta): void
    {
        $usuario = $alerta->sensor->propriedade?->usuario;

        if ($usuario && $usuario->fl_notificar_email_alerta && $usuario->email) {
            Mail::to($usuario->email)->send(new AlertaGeradoMail($alerta));
        }
    }
}
