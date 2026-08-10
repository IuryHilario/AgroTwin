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
            'tp_severidade' => 'warning',
            'ds_mensagem' => $mensagem,
            'fl_lida' => false,
            'dt_alerta' => $leitura->dt_leitura,
        ]);

        $this->notificarPorEmail($alerta);

        return $alerta;
    }

    private function notificarPorEmail(Alerta $alerta): void
    {
        $usuario = $alerta->sensor->propriedade?->usuario;

        if ($usuario && $usuario->fl_notificar_email_alerta && $usuario->email) {
            Mail::to($usuario->email)->send(new AlertaGeradoMail($alerta));
        }
    }
}
