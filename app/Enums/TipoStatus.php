<?php

namespace App\Enums;

enum TipoStatus: string
{
    case ATIVO = 'ativo';
    case INATIVO = 'inativo';
    case COLHIDA = 'colhida';
    case ENCERRADA = 'encerrada';

    public function label(): string
    {
        return match ($this) {
            self::ATIVO => 'Ativo',
            self::INATIVO => 'Inativo',
            self::COLHIDA => 'Colhida',
            self::ENCERRADA => 'Encerrada',
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
