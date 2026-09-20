<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Builder;

class AlertaEntity
{
    public const TABLE = 'alertas';

    public const PRIMARY_KEY = 'id_alerta';

    public const FILLABLE = [
        'id_sensor',
        'id_lavoura',
        'tp_direcao',
        'tp_severidade',
        'nu_ocorrencias',
        'ds_mensagem',
        'fl_lida',
        'dt_alerta',
        'dt_ultima_ocorrencia',
        'dt_normalizado',
    ];

    public const CASTS = [
        'fl_lida' => 'boolean',
        'nu_ocorrencias' => 'integer',
        'dt_alerta' => 'datetime',
        'dt_ultima_ocorrencia' => 'datetime',
        'dt_normalizado' => 'datetime',
    ];

    public static function getPorUsuario(Builder $query, $idUsuario)
    {
        return $query->whereHas('lavoura.propriedade', function ($q) use ($idUsuario) {
            $q->where('id_usuario', $idUsuario);
        })->orderByDesc('dt_alerta');
    }
}
