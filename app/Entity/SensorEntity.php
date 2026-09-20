<?php

namespace App\Entity;

use App\Enums\TipoSensor;
use App\Enums\TipoStatusSensor;

class SensorEntity
{
    public const TABLE = 'sensores';
    public const PRIMARY_KEY = 'id_sensor';
    public const FILLABLE = [
        'ds_nome',
        'tp_sensor',
        'ds_status',
        'id_propriedade',
        'id_lavoura',
        'token',
    ];

    public const CASTS = [
        'tp_sensor' => TipoSensor::class,
        'ds_status' => TipoStatusSensor::class,
    ];

}
