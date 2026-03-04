<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Builder;

class LavouraEntity
{
    public const TABLE = 'lavouras';

    public const PRIMARY_KEY = 'id_lavoura';

    public const FILLABLE = [
        'ds_cultura',
        'dt_plantio',
        'dt_colheita',
        'tp_status',
        'ds_observacao',
        'id_propriedade',
        'id_usuario',
    ];

    public const CASTS = [
        'dt_plantio' => 'date',
        'dt_colheita_prevista' => 'date',
        'tp_status' => \App\Enums\TipoStatus::class,
    ];

    public static function getLavouraById(Builder $query, $id)
    {
        return $query->select('*')
            ->where('id_lavoura', $id);
    }

    public static function getLavourasByPropriedade(Builder $query, $idPropriedade)
    {
        return $query->select('*')
            ->where('id_propriedade', $idPropriedade);
    }

    public static function getPropriedadesByIdLavoura(Builder $query, $idLavoura)
    {
        return $query->select('*')
            ->join('propriedades', 'propriedades.id_propriedade', '=', 'lavouras.id_propriedade')
            ->where('lavouras.id_lavoura', $idLavoura);
    }
}
