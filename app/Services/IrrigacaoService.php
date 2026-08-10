<?php

/**
 * Decide, a partir da última leitura de umidade do solo, se uma lavoura
 * precisa ser irrigada, e mantém o histórico de início/fim de cada irrigação
 * (manual ou automática). É o serviço que o dispositivo de irrigação
 * (ESP32 + relé/válvula solenoide) consulta para saber se deve manter a
 * válvula aberta — ver Api\IrrigacaoController.
 */

namespace App\Services;

use App\Models\ConfiguracaoLimite;
use App\Models\HistoricoIrrigacao;
use App\Models\Lavoura;
use App\Models\LeituraSensor;

class IrrigacaoService
{
    /**
     * Avalia uma nova leitura de umidade do solo e inicia/encerra a
     * irrigação automaticamente, conforme os limites configurados para a
     * lavoura. Leituras de outros parâmetros (pH, NPK, etc.) são ignoradas
     * aqui — a decisão de irrigar é sempre pela umidade do solo.
     */
    public function verificar(LeituraSensor $leitura): void
    {
        $sensor = $leitura->sensor;

        if (!$sensor || !$sensor->id_lavoura || !$sensor->tp_sensor || $sensor->tp_sensor->value !== 'umidade_solo') {
            return;
        }

        $lavoura = $sensor->lavoura;
        if (!$lavoura) {
            return;
        }

        $limite = ConfiguracaoLimite::where('id_lavoura', $lavoura->id_lavoura)
            ->where('tp_sensor', 'umidade_solo')
            ->first();

        if (!$limite || $limite->valor_min === null) {
            return;
        }

        if ($leitura->valor < $limite->valor_min) {
            $motivo = "Umidade do solo abaixo do limite mínimo ({$leitura->valor}% < {$limite->valor_min}%)";
            $this->iniciar($lavoura, 'automatico', $motivo);
        } elseif ($lavoura->fl_irrigacao_ativa) {
            $this->encerrar($lavoura);
        }
    }

    /**
     * Liga a irrigação da lavoura (se ainda não estiver ligada) e abre um
     * novo registro no histórico.
     */
    public function iniciar(Lavoura $lavoura, string $tipoAcionamento, ?string $motivo = null): void
    {
        if ($lavoura->fl_irrigacao_ativa) {
            return;
        }

        $lavoura->update(['fl_irrigacao_ativa' => true]);

        HistoricoIrrigacao::create([
            'id_lavoura' => $lavoura->id_lavoura,
            'tp_acionamento' => $tipoAcionamento,
            'dt_inicio' => now(),
            'ds_motivo' => $motivo,
        ]);
    }

    /**
     * Desliga a irrigação da lavoura (se estiver ligada) e fecha o registro
     * de histórico em aberto.
     */
    public function encerrar(Lavoura $lavoura): void
    {
        if (!$lavoura->fl_irrigacao_ativa) {
            return;
        }

        $lavoura->update(['fl_irrigacao_ativa' => false]);

        HistoricoIrrigacao::where('id_lavoura', $lavoura->id_lavoura)
            ->whereNull('dt_fim')
            ->latest('dt_inicio')
            ->first()
            ?->update(['dt_fim' => now()]);
    }
}
