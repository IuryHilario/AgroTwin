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
}
