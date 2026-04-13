<?php

namespace App\Enums;

enum TipoFinalidade: string
{
    case PREVENTIVA = 'preventiva';
    case CURATIVA = 'curativa';
    case CORRETIVA = 'corretiva';
    case NUTRICONAL = 'nutricional';
    case CRESCIMENTO = 'crescimento';
    case MANUTENCAO = 'manutencao';

    public function label(): string
    {
        return match ($this) {
            self::PREVENTIVA => 'Preventiva',
            self::CURATIVA => 'Curativa',
            self::CORRETIVA => 'Corretiva',
            self::NUTRICONAL => 'Nutricional',
            self::CRESCIMENTO => 'Crescimento',
            self::MANUTENCAO => 'Manutenção',
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
