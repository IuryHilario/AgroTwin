<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Builder;

class HistoricoIrrigacaoEntity
{
    public const TABLE = 'historico_irrigacao';

    public const PRIMARY_KEY = 'id_irrigacao';

    public const FILLABLE = [
        'id_lavoura',
        'tp_acionamento',
        'dt_inicio',
        'dt_fim',
        'ds_motivo',
    ];

    public const CASTS = [
        'dt_inicio' => 'datetime',
        'dt_fim' => 'datetime',
    ];

    public static function getPorLavoura(Builder $query, $idLavoura)
    {
        return $query->where('id_lavoura', $idLavoura)->orderByDesc('dt_inicio');
    }
}
