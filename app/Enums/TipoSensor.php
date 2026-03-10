<?php

namespace App\Enums;

enum TipoSensor: string
{
    case UMIDADE_SOLO = 'umidade_solo';
    case TEMPERATURA = 'temperatura';
    case NITROGENIO = 'nitrogenio';
    case FOSFORO = 'fosforo';
    case POTASSIO = 'potassio';
    case PH = 'ph';
    case NPK = 'npk';

    public function label(): string
    {
        return match ($this) {
            self::UMIDADE_SOLO => 'Umidade do Solo',
            self::TEMPERATURA => 'Temperatura',
            self::NITROGENIO => 'Nitrogênio',
            self::FOSFORO => 'Fósforo',
            self::POTASSIO => 'Potássio',
            self::PH => 'pH',
            self::NPK => 'NPK',
        };
    }
}