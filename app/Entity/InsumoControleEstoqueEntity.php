<?php

namespace App\Entity;

class InsumoControleEstoqueEntity
{
    public const TABLE = 'insumos_controle_estoque';

    public const PRIMARY_KEY = 'id_controle_estoque';

    public const FILLABLE = [
        'id_insumo',
        'tp_controle',
        'nu_quantidade',
        'dt_movimentacao',
        'vl_unitario',
        'ds_documento',
        'ds_fornecedor',
        'ds_observacao',
    ];

    public const CASTS = [
        'tp_controle' => \App\Enums\TipoMovimentacao::class,
        'nu_quantidade' => 'decimal:2',
        'vl_unitario' => 'decimal:2',
        'dt_movimentacao' => 'date',
    ];
}
