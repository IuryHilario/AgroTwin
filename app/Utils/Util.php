<?php

namespace App\Utils;

use Carbon\Carbon;

/**
 * Formatação no padrão brasileiro, usada pelas listagens
 * (componente <x-ui.celula>). Valores vazios voltam como null para o
 * componente decidir como exibir a ausência de dado.
 */
class Util
{
    public static function formatDate($data): ?string
    {
        return self::formatarData($data, 'd/m/Y');
    }

    public static function formatDateTime($dataHora): ?string
    {
        return self::formatarData($dataHora, 'd/m/Y H:i');
    }

    public static function formatNumber($valor, int $casas = 2): ?string
    {
        if ($valor === null || $valor === '' || !is_numeric($valor)) {
            return null;
        }

        return number_format((float) $valor, $casas, ',', '.');
    }

    private static function formatarData($valor, string $formato): ?string
    {
        if (empty($valor)) {
            return null;
        }

        try {
            return Carbon::parse($valor)->format($formato);
        } catch (\Exception) {
            return (string) $valor;
        }
    }
}
