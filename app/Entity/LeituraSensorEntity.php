<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Builder;

class LeituraSensorEntity
{
    public const TABLE = 'leituras_sensor';

    public const PRIMARY_KEY = 'id_leitura';

    public const FILLABLE = [
        'id_sensor',
        'valor',
        'dt_leitura',
    ];

    public const CASTS = [
        'valor' => 'float',
        'dt_leitura' => 'datetime',
    ];

    public static function getUltimaLeitura(Builder $query, $idSensor)
    {
        return $query->where('id_sensor', $idSensor)
            ->orderByDesc('dt_leitura');
    }

    public static function getHistorico(Builder $query, $idSensor, $desde)
    {
        return $query->where('id_sensor', $idSensor)
            ->where('dt_leitura', '>=', $desde)
            ->orderBy('dt_leitura');
    }
}
