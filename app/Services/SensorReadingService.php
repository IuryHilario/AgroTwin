<?php

/**
 * Único ponto de escrita/leitura das leituras de sensores.
 * Hoje armazena em MariaDB (leituras_sensor); se o projeto migrar para
 * InfluxDB no futuro, é só reimplementar os métodos desta classe —
 * nenhum outro lugar do sistema fala diretamente com o model LeituraSensor.
 */

namespace App\Services;

use App\Entity\LeituraSensorEntity;
use App\Models\LeituraSensor;
use App\Models\Sensor;
use DateTimeInterface;
use Illuminate\Support\Collection;

class SensorReadingService
{
    public function registrar(Sensor $sensor, float $valor, ?DateTimeInterface $dataHora = null): LeituraSensor
    {
        return LeituraSensor::create([
            'id_sensor' => $sensor->id_sensor,
            'valor' => $valor,
            'dt_leitura' => $dataHora ?? now(),
        ]);
    }

    public function ultimaLeitura(Sensor $sensor): ?LeituraSensor
    {
        return LeituraSensorEntity::getUltimaLeitura(LeituraSensor::query(), $sensor->id_sensor)->first();
    }

    public function historico(Sensor $sensor, DateTimeInterface $desde): Collection
    {
        return LeituraSensorEntity::getHistorico(LeituraSensor::query(), $sensor->id_sensor, $desde)->get();
    }

    public function historicoEntrePeriodo(Sensor $sensor, DateTimeInterface $inicio, DateTimeInterface $fim): Collection
    {
        return LeituraSensorEntity::getEntrePeriodo(LeituraSensor::query(), $sensor->id_sensor, $inicio, $fim)->get();
    }

    /**
     * Estatísticas do período: mínimo, máximo, média e quantidade de leituras.
     */
    public function resumoEstatistico(Collection $leituras): array
    {
        return [
            'minimo' => $leituras->isEmpty() ? null : round($leituras->min('valor'), 2),
            'maximo' => $leituras->isEmpty() ? null : round($leituras->max('valor'), 2),
            'media' => $leituras->isEmpty() ? null : round($leituras->avg('valor'), 2),
            'quantidade' => $leituras->count(),
        ];
    }

    /**
     * Série com a média diária das leituras, pronta para alimentar um gráfico
     * de linha (uma leitura por dia em vez de todos os pontos brutos).
     */
    public function serieDiaria(Collection $leituras): Collection
    {
        return $leituras
            ->groupBy(fn (LeituraSensor $leitura) => $leitura->dt_leitura->format('Y-m-d'))
            ->map(fn (Collection $doDia) => round($doDia->avg('valor'), 2))
            ->sortKeys();
    }
}
