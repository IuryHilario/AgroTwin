<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Builder;

class InsumoEntity
{
    public const TABLE = 'insumos';

    public const PRIMARY_KEY = 'id_insumo';

    public const FILLABLE = [
        'ds_nome',
        'tp_insumo',
        'ds_fabricante',
        'tp_unidade_medida',
        'dt_validade',
        'nu_estoque_minimo',
        'status',
        'ds_composicao',
        'id_usuario',
    ];

    public const CASTS = [
        'dt_validade' => 'date',
        'tp_insumo' => \App\Enums\TipoInsumo::class,
        'tp_unidade_medida' => \App\Enums\TipoUnidadeMedida::class,
    ];

    public static function getIdInsumo()
    {
        return InsumoEntity::PRIMARY_KEY;
    }


}
