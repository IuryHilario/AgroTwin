<?php

namespace App\Utils;

class Util
{
    /**
     * Formata uma data para o padrão DD/MM/AAAA
     */
    public static function formatDate($date)
    {
        if (empty($date)) return '-';
        try {
            return \Carbon\Carbon::parse($date)->format('d/m/Y');
        } catch (\Exception $e) {
            return $date;
        }
    }

    /**
     * Formata uma data para o padrão DD/MM/AAAA HH:MM
     */
    public static function formatDateTime($datetime)
    {
        if (empty($datetime)) return '-';
        try {
            return \Carbon\Carbon::parse($datetime)->format('d/m/Y H:i');
        } catch (\Exception $e) {
            return $datetime;
        }
    }

    /**
     * Formata um valor monetário para o padrão brasileiro
     */
    public static function formatMoney($value)
    {
        if (empty($value) || !is_numeric($value)) return 'R$ 0,00';
        return 'R$ ' . number_format($value, 2, ',', '.');
    }

    /**
     * Formata um número com separadores de milhares
     */
    public static function formatNumber($value)
    {
        if (empty($value) || !is_numeric($value)) return '0';
        return number_format($value, 0, ',', '.');
    }

    /**
     * Formata um texto para capitalizar primeira letra de cada palavra
     */
    public static function formatTitle($text)
    {
        if (empty($text)) return '-';
        return mb_convert_case($text, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Formata um valor de área (hectares)
     */
    public static function formatArea($value)
    {
        if (empty($value) || !is_numeric($value)) return '0 ha';
        return number_format($value, 2, ',', '.') . ' ha';
    }

    /**
     * Formata um CPF/CNPJ removendo caracteres especiais
     */
    public static function formatDocument($document)
    {
        if (empty($document)) return '-';
        $clean = preg_replace('/[^0-9]/', '', $document);

        if (strlen($clean) == 11) {
            // CPF
            return substr($clean, 0, 3) . '.' . substr($clean, 3, 3) . '.' .
                   substr($clean, 6, 3) . '-' . substr($clean, 9, 2);
        } elseif (strlen($clean) == 14) {
            // CNPJ
            return substr($clean, 0, 2) . '.' . substr($clean, 2, 3) . '.' .
                   substr($clean, 5, 3) . '/' . substr($clean, 8, 4) . '-' . substr($clean, 12, 2);
        }

        return $document;
    }

    /**
     * Formata um telefone com máscara
     */
    public static function formatPhone($phone)
    {
        if (empty($phone)) return '-';
        $clean = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($clean) == 11) {
            return '(' . substr($clean, 0, 2) . ') ' . substr($clean, 2, 5) . '-' . substr($clean, 7, 4);
        } elseif (strlen($clean) == 10) {
            return '(' . substr($clean, 0, 2) . ') ' . substr($clean, 2, 4) . '-' . substr($clean, 6, 4);
        }

        return $phone;
    }

    /**
     * Formata um peso em quilogramas
     */
    public static function formatWeight($value)
    {
        if (empty($value) || !is_numeric($value)) return '0 kg';
        return number_format($value, 2, ',', '.') . ' kg';
    }

    /**
     * Formata uma produtividade (kg/ha ou t/ha)
     */
    public static function formatProductivity($value, $unit = 'kg/ha')
    {
        if (empty($value) || !is_numeric($value)) return '0 ' . $unit;
        return number_format($value, 2, ',', '.') . ' ' . $unit;
    }

    /**
     * Formata uma temperatura em Celsius
     */
    public static function formatTemperature($value)
    {
        if (empty($value) || !is_numeric($value)) return '0°C';
        return number_format($value, 1, ',', '.') . '°C';
    }

    /**
     * Formata percentual
     */
    public static function formatPercentage($value)
    {
        if (empty($value) || !is_numeric($value)) return '0%';
        return number_format($value, 2, ',', '.') . '%';
    }

    /**
     * Calcula idade em anos a partir de uma data
     */
    public static function calculateAge($birthDate)
    {
        if (empty($birthDate)) return 0;
        try {
            return \Carbon\Carbon::parse($birthDate)->age;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Valida se um email é válido
     */
    public static function isValidEmail($email)
    {
        if (empty($email)) return false;
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valida se um CPF é válido
     */
    public static function isValidCPF($cpf)
    {
        if (empty($cpf)) return false;

        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    /**
     * Valida se um CNPJ é válido
     */
    public static function isValidCNPJ($cnpj)
    {
        if (empty($cnpj)) return false;

        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        if (strlen($cnpj) != 14) {
            return false;
        }

        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $weights1[$i];
        }
        $digit1 = ($sum % 11 < 2) ? 0 : 11 - ($sum % 11);

        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $weights2[$i];
        }
        $digit2 = ($sum % 11 < 2) ? 0 : 11 - ($sum % 11);

        return ($cnpj[12] == $digit1 && $cnpj[13] == $digit2);
    }

    /**
     * Remove acentos de uma string
     */
    public static function removeAccents($string)
    {
        if (empty($string)) return '';

        $accents = [
            'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A',
            'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a',
            'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E',
            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e',
            'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I',
            'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i',
            'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O',
            'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o',
            'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U',
            'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u',
            'Ç'=>'C', 'ç'=>'c', 'Ñ'=>'N', 'ñ'=>'n'
        ];

        return strtr($string, $accents);
    }

    /**
     * Calcula diferença em dias entre duas datas
     */
    public static function daysBetween($startDate, $endDate)
    {
        if (empty($startDate) || empty($endDate)) return 0;

        try {
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);
            return $start->diffInDays($end);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Formata um período (data início - data fim)
     */
    public static function formatPeriod($startDate, $endDate)
    {
        if (empty($startDate) && empty($endDate)) return '-';
        if (empty($startDate)) return 'Até ' . self::formatDate($endDate);
        if (empty($endDate)) return 'A partir de ' . self::formatDate($startDate);

        return self::formatDate($startDate) . ' até ' . self::formatDate($endDate);
    }

    /**
     * Formata coordenadas geográficas (latitude/longitude)
     */
    public static function formatCoordinates($latitude, $longitude)
    {
        if (empty($latitude) || empty($longitude)) return '-';
        if (!is_numeric($latitude) || !is_numeric($longitude)) return '-';

        return number_format($latitude, 6, ',', '.') . ', ' . number_format($longitude, 6, ',', '.');
    }
}
