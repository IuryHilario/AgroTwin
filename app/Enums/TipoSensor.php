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
    case CONDUTIVIDADE = 'condutividade';

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
            self::CONDUTIVIDADE => 'Condutividade Elétrica',
        };
    }

    public function unidade(): string
    {
        return match ($this) {
            self::UMIDADE_SOLO => '%',
            self::TEMPERATURA => '°C',
            self::NITROGENIO, self::FOSFORO, self::POTASSIO => 'mg/dm³',
            self::PH => '',
            self::NPK => 'mg/dm³',
            self::CONDUTIVIDADE => 'µS/cm',
        };
    }

    public static function toSelectArray($blank = false): array
    {
        $options = collect(self::cases())->mapWithKeys(function (self $enum) {
            return [$enum->value => $enum->label()];
        })->toArray();

        if ($blank) {
            return ['' => '---------- Selecione ----------'] + $options;
        }

        return $options;
    }
}