<?php

namespace App\Enums;

enum TipoUnidadeMedida: string
{
    case KG = 'kg';
    case LITRO = 'litro';
    case UNIDADE = 'unidade';
    case TONELADA = 'tonelada';
    case SACO = 'saco';

    public function label(): string
    {
        return match ($this) {
            self::KG => 'Kilograma',
            self::LITRO => 'Litro',
            self::UNIDADE => 'Unidade',
            self::TONELADA => 'Tonelada',
            self::SACO => 'Saco',
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
