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
        'id_usuario',
        'tp_solo',
        'nu_area_hectares',
        'fl_inativo',
    ];

    public const CASTS = [
        'tp_solo' => \App\Enums\TipoSolo::class,
        'fl_inativo' => 'boolean',
    ];

    public static function getPropriedadeById(Builder $query, $id)
    {
        return $query->select(
            'id_propriedade',
            'ds_nome',
            'ds_localizacao',
            'tp_solo',
            'nu_area_hectares',
            'fl_inativo',
            'id_usuario',
            'created_at',
            'updated_at'
        )
            ->where('id_propriedade', $id);
    }

    public static function getPropriedadesByUsuario(Builder $query, $idUsuario)
    {
        return $query->select(
            'id_propriedade',
            'ds_nome',
            'ds_localizacao',
            'tp_solo',
            'nu_area_hectares',
            'fl_inativo',
            'id_usuario',
            'created_at',
            'updated_at'
        )
            ->where('id_usuario', $idUsuario);
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
