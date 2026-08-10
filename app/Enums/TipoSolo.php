<?php

namespace App\Enums;

enum TipoSolo: string
{
    case LATOSOLO_VERMELHO = 'latosolo_vermelho';
    case ARGILOSO = 'argiloso';
    case NEOSOLO = 'neosolo';
    case CAMBISOLO = 'cambisolo';
    case GLEISSOLO = 'gleissolo';

    public function label(): string
    {
        return match ($this) {
            self::LATOSOLO_VERMELHO => 'Latosolo Vermelho',
            self::ARGILOSO => 'Argiloso',
            self::NEOSOLO => 'Neosolo',
            self::CAMBISOLO => 'Cambissolo',
            self::GLEISSOLO => 'Gleissolo',
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
