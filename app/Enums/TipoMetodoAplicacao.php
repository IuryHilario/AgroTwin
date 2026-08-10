<?php

namespace App\Enums;

enum TipoMetodoAplicacao: string
{
    case PULVERIZACAO_TERRESTRE = 'pulverizacao_terrestre';
    case PULVERIZACAO_AEREA = 'pulverizacao_aerea';
    case APLICACAO_LOCALIZADA = 'aplicacao_localizada';
    case FERTIRRIGACAO = 'fertirrigacao';

    public function label(): string
    {
        return match ($this) {
            self::PULVERIZACAO_TERRESTRE => 'Pulverização Terrestre',
            self::PULVERIZACAO_AEREA => 'Pulverização Aérea',
            self::APLICACAO_LOCALIZADA => 'Aplicação Localizada',
            self::FERTIRRIGACAO => 'Fertirrigação',
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
