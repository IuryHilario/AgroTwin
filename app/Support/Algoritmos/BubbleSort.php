<?php

namespace App\Support\Algoritmos;

/**
 * Bubble Sort aplicado às leituras de sensores do AgroTwin.
 *
 * Ordena um lote de leituras (ex.: leituras reenviadas fora de ordem
 * pelo ESP32, ou o histórico de um período ordenado pelo valor medido).
 *
 * Cada elemento: ['timestamp' => int, 'valor' => float]
 * n = quantidade de leituras do lote.
 *
 * Versão otimizada: se uma passada inteira não fizer nenhuma troca,
 * a lista já está ordenada e o algoritmo para (melhor caso O(n)).
 */
class BubbleSort
{
    /** Contadores usados na análise de complexidade. */
    public int $comparacoes = 0;
    public int $trocas = 0;
    public int $passadas = 0;

    /**
     * @param  array<int, array{timestamp:int, valor:float}>  $leituras
     * @param  string  $chave  'timestamp' (ordem cronológica) ou 'valor' (ranking)
     * @return array<int, array{timestamp:int, valor:float}>  nova lista ordenada (crescente)
     */
    public function ordenar(array $leituras, string $chave = 'timestamp'): array
    {
        $this->comparacoes = 0;
        $this->trocas = 0;
        $this->passadas = 0;

        $lista = array_values($leituras);
        $n = count($lista);

        for ($fim = $n - 1; $fim > 0; $fim--) {
            $this->passadas++;
            $trocou = false;

            // a cada passada, o maior elemento "borbulha" até a posição $fim
            for ($j = 0; $j < $fim; $j++) {
                $this->comparacoes++;
                if ($lista[$j][$chave] > $lista[$j + 1][$chave]) {
                    $aux = $lista[$j];
                    $lista[$j] = $lista[$j + 1];
                    $lista[$j + 1] = $aux;
                    $this->trocas++;
                    $trocou = true;
                }
            }

            if (! $trocou) {
                break; // nenhuma troca: lista já ordenada
            }
        }

        return $lista;
    }
}
