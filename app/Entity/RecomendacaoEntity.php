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
        'dt_recomendacao',
    ];

    public const CASTS = [
        'dt_recomendacao' => 'datetime',
    ];

    public static function getPorLavoura(Builder $query, $idLavoura)
    {
        return $query->where('id_lavoura', $idLavoura)->orderByDesc('dt_recomendacao');
    }
}
