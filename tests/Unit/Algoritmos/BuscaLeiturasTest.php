<?php

namespace Tests\Unit\Algoritmos;

use App\Support\Algoritmos\BuscaLeituras;
use PHPUnit\Framework\TestCase;

class BuscaLeiturasTest extends TestCase
{
    private function leituras(array $timestamps): array
    {
        return array_map(fn ($t) => ['timestamp' => $t, 'valor' => 40.0], $timestamps);
    }

    public function test_busca_linear_encontra_60_com_6_verificacoes(): void
    {
        $busca = new BuscaLeituras();

        $this->assertSame(5, $busca->linear($this->leituras([10, 20, 30, 40, 50, 60, 70]), 60));
        $this->assertSame(6, $busca->verificacoes);
    }

    public function test_busca_binaria_encontra_70_com_3_verificacoes(): void
    {
        $busca = new BuscaLeituras();

        $this->assertSame(6, $busca->binaria($this->leituras([10, 20, 30, 40, 50, 60, 70, 80]), 70));
        $this->assertSame(3, $busca->verificacoes);
    }

    public function test_leitura_inexistente_retorna_menos_um(): void
    {
        $busca = new BuscaLeituras();
        $lista = $this->leituras([10, 20, 30]);

        $this->assertSame(-1, $busca->linear($lista, 25));
        $this->assertSame(3, $busca->verificacoes);
        $this->assertSame(-1, $busca->binaria($lista, 25));
    }
}
