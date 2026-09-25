<?php

namespace Tests\Unit\Algoritmos;

use App\Support\Algoritmos\RelatorioDesempenho;
use PHPUnit\Framework\TestCase;

class RelatorioDesempenhoTest extends TestCase
{
    public function test_classifica_o_crescimento_das_operacoes(): void
    {
        $this->assertSame('O(1)', RelatorioDesempenho::classificar(1000, 1, 5000, 1));
        $this->assertSame('O(log n)', RelatorioDesempenho::classificar(1000, 10, 5000, 13));
        $this->assertSame('O(n)', RelatorioDesempenho::classificar(1000, 999, 5000, 4999));
        $this->assertSame('O(n²)', RelatorioDesempenho::classificar(1000, 499500, 5000, 12497500));
    }
}
