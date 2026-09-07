<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Builder;

class RecomendacaoEntity
{
    public const TABLE = 'recomendacoes';

    public const PRIMARY_KEY = 'id_recomendacao';

    public const FILLABLE = [
        'id_lavoura',
        'tp_sensor',
        'ds_recomendacao',
        'nu_valor_leitura',
        'nu_limite_min',
        'nu_limite_max',
        'dt_recomendacao',
    ];

    public const CASTS = [
        'tp_sensor' => \App\Enums\TipoSensor::class,
        'nu_valor_leitura' => 'float',
        'nu_limite_min' => 'float',
        'nu_limite_max' => 'float',
        'dt_recomendacao' => 'datetime',
    ];

    public static function getPorLavoura(Builder $query, $idLavoura)
    {
        return $query->where('id_lavoura', $idLavoura)->orderByDesc('dt_recomendacao');
    }

    public static function getPorUsuario(Builder $query, $idUsuario)
    {
        return $query->whereHas('lavoura.propriedade', function ($q) use ($idUsuario) {
            $q->where('id_usuario', $idUsuario);
        })->orderByDesc('dt_recomendacao');
    }
}
