<?php

namespace App\Enums;

enum TipoInsumo: string
{
    case FERTILIZANTE = 'fertilizante';
    case DEFENSIVO_AGRICOLA = 'defensivo_agricola';
    case SEMENTE = 'semente';
    case ADUBO_ORGANICO = 'adubo_organico';
    case CORRETIVO_SOLO = 'corretivo_solo';

    public function label(): string
    {
        return match ($this) {
            self::FERTILIZANTE => 'Fertilizante',
            self::DEFENSIVO_AGRICOLA => 'Defensivo Agrícola',
            self::SEMENTE => 'Semente',
            self::ADUBO_ORGANICO => 'Adubo Orgânico',
            self::CORRETIVO_SOLO => 'Corretivo de Solo',
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
