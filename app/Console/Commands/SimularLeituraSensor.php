<?php

namespace App\Console\Commands;

use App\Models\Sensor;
use App\Services\AlertaService;
use App\Services\IrrigacaoService;
use App\Services\RecomendacaoService;
use App\Services\SensorReadingService;
use Illuminate\Console\Command;

class SimularLeituraSensor extends Command
{
    // Comando: php artisan sensores:simular-leitura

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sensores:simular-leitura {--sensor= : ID de um sensor específico (padrão: todos)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera leituras plausíveis para os sensores cadastrados, para testar o dashboard/alertas antes do hardware chegar';



    /**
     * Faixas de valores plausíveis por tipo de sensor, usadas para gerar dados de teste.
     */
    private const FAIXAS = [
        'umidade_solo' => [20, 80],
        'temperatura' => [15, 35],
        'ph' => [4.5, 8.0],
        'nitrogenio' => [5, 50],
        'fosforo' => [5, 50],
        'potassio' => [5, 50],
        'npk' => [5, 50],
        'condutividade' => [200, 2000],
    ];

    public function handle(
        SensorReadingService $leituraService,
        AlertaService $alertaService,
        RecomendacaoService $recomendacaoService,
        IrrigacaoService $irrigacaoService
    ): int {
        $query = Sensor::query();

        if ($idSensor = $this->option('sensor')) {
            $query->where('id_sensor', $idSensor);
        }

        $sensores = $query->get();

        if ($sensores->isEmpty()) {
            $this->warn('Nenhum sensor cadastrado.');
            return self::SUCCESS;
        }

        foreach ($sensores as $sensor) {
            if (!$sensor->tp_sensor) {
                continue;
            }

            [$min, $max] = self::FAIXAS[$sensor->tp_sensor->value] ?? [0, 100];
            $valor = round($min + mt_rand() / mt_getrandmax() * ($max - $min), 2);

            $leitura = $leituraService->registrar($sensor, $valor);
            $alerta = $alertaService->verificar($leitura);
            $recomendacaoService->avaliarERegistrar($sensor, $valor);
            $irrigacaoService->verificar($leitura);

            $this->line("Sensor #{$sensor->id_sensor} ({$sensor->ds_nome}): {$valor}{$sensor->tp_sensor->unidade()}"
                . ($alerta ? ' — ⚠ alerta gerado' : ''));
        }

        $this->info("Leituras simuladas para {$sensores->count()} sensor(es).");

        return self::SUCCESS;
    }
}
