<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Builder;

class PropriedadeEntity
{
    public const TABLE = 'propriedades';

    public const PRIMARY_KEY = 'id_propriedade';

    public const FILLABLE = [
        'ds_nome',
        'ds_localizacao',
        'nu_latitude',
        'nu_longitude',
        'id_usuario',
        'tp_solo',
        'nu_area_hectares',
        'fl_inativo',
    ];

    public const CASTS = [
        'tp_solo' => \App\Enums\TipoSolo::class,
        'fl_inativo' => 'boolean',
        'nu_latitude' => 'float',
        'nu_longitude' => 'float',
    ];

    /**
     * Sem lista fixa de colunas de propósito: ela descartava em silêncio toda
     * coluna nova (latitude/longitude sumiam no seletor do dashboard).
     */
    public static function getPropriedadesByUsuario(Builder $query, $idUsuario)
    {
        return $query->where('id_usuario', $idUsuario);
    }

    public static function getNomePropriedadeByIdUsuario(Builder $query, $id)
    {
        return $query->select(
            'id_propriedade',
            'ds_nome'
        )
            ->where('id_usuario', $id);
    }

    public static function pluckNomeByUsuario(Builder $query, $id)
    {
        return $query->where('id_usuario', $id)->where('fl_inativo', false)->pluck('ds_nome', 'id_propriedade');
    }
}
