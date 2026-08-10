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
        'tp_severidade',
        'ds_mensagem',
        'fl_lida',
        'dt_alerta',
    ];

    public const CASTS = [
        'fl_lida' => 'boolean',
        'dt_alerta' => 'datetime',
    ];

    public static function getPorUsuario(Builder $query, $idUsuario)
    {
        return $query->whereHas('lavoura.propriedade', function ($q) use ($idUsuario) {
            $q->where('id_usuario', $idUsuario);
        })->orderByDesc('dt_alerta');
    }
}
