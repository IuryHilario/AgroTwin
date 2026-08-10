<?php

namespace App\Entity;

use App\Enums\TipoSensor;
use Illuminate\Database\Eloquent\Builder;

class ConfiguracaoLimiteEntity
{
    public const TABLE = 'configuracoes_limites';

    public const PRIMARY_KEY = 'id';

    public const FILLABLE = [
        'id_lavoura',
        'tp_sensor',
        'valor_min',
        'valor_max',
    ];

    public const CASTS = [
        'tp_sensor' => TipoSensor::class,
        'valor_min' => 'float',
        'valor_max' => 'float',
    ];

    public static function getPorLavoura(Builder $query, $idLavoura)
    {
        return $query->where('id_lavoura', $idLavoura);
    }
}
