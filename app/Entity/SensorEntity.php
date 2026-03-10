<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Builder;

class SensorEntity
{
    public const TABLE = 'sensores';
    public const PRIMARY_KEY = 'id_sensor';
    public const FILLABLE = [
        'ds_nome',
        'tp_sensor',
        'ds_status',
        'id_propriedade',
    ];
}
