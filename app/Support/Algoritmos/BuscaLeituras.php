<?php

namespace App\Support\Algoritmos;

/**
 * Estruturas de busca sobre uma lista de leituras.
 * Cada elemento: ['timestamp' => int, 'valor' => float]
 * Retornam o índice da leitura com o timestamp procurado, ou -1.
 */
class BuscaLeituras
{
    /** Quantas leituras foram verificadas na última busca. */
    public int $verificacoes = 0;

    /** Busca linear: verifica uma a uma, do início ao fim. Funciona em qualquer lista. */
    public function linear(array $leituras, int $timestamp): int
    {
        $this->verificacoes = 0;

        foreach ($leituras as $indice => $leitura) {
            $this->verificacoes++;
            if ($leitura['timestamp'] === $timestamp) {
                return $indice;
            }
        }

        return -1;
    }

    /** Busca binária: exige lista ORDENADA por timestamp; descarta metade a cada passo. */
    public function binaria(array $leituras, int $timestamp): int
    {
        $this->verificacoes = 0;
        $inicio = 0;
        $fim = count($leituras) - 1;

        while ($inicio <= $fim) {
            $meio = intdiv($inicio + $fim, 2);
            $this->verificacoes++;
            $atual = $leituras[$meio]['timestamp'];

            if ($atual === $timestamp) {
                return $meio;
            }
            if ($atual < $timestamp) {
                $inicio = $meio + 1;   // descarta a metade esquerda
            } else {
                $fim = $meio - 1;      // descarta a metade direita
            }
        }

        return -1;
    }
}
