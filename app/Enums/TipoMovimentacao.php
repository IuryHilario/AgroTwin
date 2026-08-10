<?php

namespace App\Enums;

enum TipoMovimentacao: string
{
    case ENTRADA = 'entrada';
    case SAIDA = 'saida';

    public function label(): string
    {
        return match ($this) {
            self::ENTRADA => 'Entrada',
            self::SAIDA => 'Saída',
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
