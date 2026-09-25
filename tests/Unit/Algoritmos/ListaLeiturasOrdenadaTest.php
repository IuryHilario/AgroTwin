<?php

namespace Tests\Unit\Algoritmos;

use App\Support\Algoritmos\ListaLeiturasOrdenada;
use PHPUnit\Framework\TestCase;

class ListaLeiturasOrdenadaTest extends TestCase
{
    private function listaCom(array $timestamps): ListaLeiturasOrdenada
    {
        $lista = new ListaLeiturasOrdenada();
        foreach ($timestamps as $t) {
            $lista->inserirOrdenado($t, 40.0);
        }
        return $lista;
    }

    public function test_inserir_no_final_melhor_caso(): void
    {
        $lista = $this->listaCom([10, 20, 30, 40]);
        $lista->inserirOrdenado(50, 45.2);

        $this->assertSame([10, 20, 30, 40, 50], $lista->timestamps());
        $this->assertSame(0, $lista->deslocamentos);
        $this->assertSame(1, $lista->comparacoes);
    }

    public function test_inserir_no_meio_caso_medio(): void
    {
        $lista = $this->listaCom([10, 20, 40, 50]);
        $lista->inserirOrdenado(30, 38.7);

        $this->assertSame([10, 20, 30, 40, 50], $lista->timestamps());
        $this->assertSame(2, $lista->deslocamentos);
    }

    public function test_inserir_no_inicio_pior_caso(): void
    {
        $lista = $this->listaCom([20, 30, 40, 50]);
        $lista->inserirOrdenado(10, 31.0);

        $this->assertSame([10, 20, 30, 40, 50], $lista->timestamps());
        $this->assertSame(4, $lista->deslocamentos);
        $this->assertSame(31.0, $lista->valorEm(0));
    }
}
