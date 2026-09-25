<?php

namespace Tests\Unit\Algoritmos;

use App\Support\Algoritmos\BubbleSort;
use PHPUnit\Framework\TestCase;

class BubbleSortTest extends TestCase
{
    private function leituras(array $timestamps): array
    {
        return array_map(fn ($t) => ['timestamp' => $t, 'valor' => 40.0], $timestamps);
    }

    public function test_lista_ja_ordenada_melhor_caso(): void
    {
        $bs = new BubbleSort();
        $r = $bs->ordenar($this->leituras([10, 20, 30, 40, 50]));

        $this->assertSame([10, 20, 30, 40, 50], array_column($r, 'timestamp'));
        $this->assertSame(4, $bs->comparacoes);   // n - 1
        $this->assertSame(0, $bs->trocas);
        $this->assertSame(1, $bs->passadas);
    }

    public function test_lista_invertida_pior_caso(): void
    {
        $bs = new BubbleSort();
        $r = $bs->ordenar($this->leituras([50, 40, 30, 20, 10]));

        $this->assertSame([10, 20, 30, 40, 50], array_column($r, 'timestamp'));
        $this->assertSame(10, $bs->comparacoes);  // n(n-1)/2
        $this->assertSame(10, $bs->trocas);
    }

    public function test_lista_embaralhada(): void
    {
        $bs = new BubbleSort();
        $r = $bs->ordenar($this->leituras([30, 10, 50, 20, 40]));

        $this->assertSame([10, 20, 30, 40, 50], array_column($r, 'timestamp'));
        $this->assertSame(4, $bs->trocas);
    }

    public function test_ordena_pelo_valor_medido(): void
    {
        $bs = new BubbleSort();
        $r = $bs->ordenar([
            ['timestamp' => 1, 'valor' => 45.1],
            ['timestamp' => 2, 'valor' => 22.8],
            ['timestamp' => 3, 'valor' => 61.0],
        ], 'valor');

        $this->assertSame([22.8, 45.1, 61.0], array_column($r, 'valor'));
    }
}
