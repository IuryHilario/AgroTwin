<?php

namespace App\Support\Algoritmos;

/**
 * Inserção Ordenada: lista de leituras de UM sensor, mantida sempre
 * ordenada pelo instante da leitura (timestamp).
 *
 * Cada elemento: ['timestamp' => int, 'valor' => float]
 * n = quantidade de leituras guardadas na lista.
 */
class ListaLeiturasOrdenada
{
    /** @var array<int, array{timestamp:int, valor:float}> */
    private array $leituras = [];

    /** Contadores usados na análise de complexidade. */
    public int $comparacoes = 0;
    public int $deslocamentos = 0;

    /**
     * Inserção ordenada: localiza a posição, desloca e insere.
     * Retorna a posição (índice) onde a leitura entrou.
     */
    public function inserirOrdenado(int $timestamp, float $valor): int
    {
        $this->comparacoes = 0;
        $this->deslocamentos = 0;

        $novo = ['timestamp' => $timestamp, 'valor' => $valor];
        $i = count($this->leituras) - 1;   // começa pelo último elemento

        // 1) comparar  2) deslocar para a direita enquanto o anterior for maior
        while ($i >= 0) {
            $this->comparacoes++;
            if ($this->leituras[$i]['timestamp'] <= $timestamp) {
                break;                      // achou a posição correta
            }
            $this->leituras[$i + 1] = $this->leituras[$i];
            $this->deslocamentos++;
            $i--;
        }

        // 3) inserir o novo elemento na posição aberta
        $this->leituras[$i + 1] = $novo;

        return $i + 1;
    }

    /** @return int[] apenas os timestamps, na ordem atual */
    public function timestamps(): array
    {
        return array_column($this->leituras, 'timestamp');
    }

    public function valorEm(int $indice): float
    {
        return $this->leituras[$indice]['valor'];
    }

    /** @return array<int, array{timestamp:int, valor:float}> */
    public function leituras(): array
    {
        return $this->leituras;
    }

    public function tamanho(): int
    {
        return count($this->leituras);
    }
}
