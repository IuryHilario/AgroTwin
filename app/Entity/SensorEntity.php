<?php

namespace App\Entity;

use App\Enums\TipoSensor;
use App\Enums\TipoStatusSensor;
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
        'id_lavoura',
        'token',
    ];

    public const CASTS = [
        'tp_sensor' => TipoSensor::class,
        'ds_status' => TipoStatusSensor::class,
    ];

    public static function getSensorById(Builder $query, $id)
    {
        return $query->select('*')
            ->where('id_sensor', $id);
    }

    public static function getSensoresByPropriedade(Builder $query, $idPropriedade)
    {
        return $query->select('*')
            ->where('id_propriedade', $idPropriedade);
    }

    public static function getSensoresByLavoura(Builder $query, $idLavoura)
    {
        return $query->select('*')
            ->where('id_lavoura', $idLavoura);
    }
}
