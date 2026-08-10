<?php

namespace App\Entity;

class InsumoAplicacaoEntity
{
    public const TABLE = 'insumos_aplicacao';
    public const PRIMARY_KEY = 'id_insumo_aplicacao';
    public const FILLABLE = [
        'id_insumo',
        'id_lavoura',
        'dt_aplicacao',
        'nu_area_aplicada',
        'nu_dosagem_hectare',
        'nu_quantidade_aplicada',
        'nu_concentracao',
        'tp_metodo_aplicacao',
        'ds_equipamento',
        'ds_responsavel',
        'tp_finalidade',
        'ds_observacoes'
    ];

}