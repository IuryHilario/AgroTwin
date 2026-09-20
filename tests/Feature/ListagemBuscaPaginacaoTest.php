<?php

namespace Tests\Feature;

use App\Models\Sensor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioSensor;
use Tests\TestCase;

class ListagemBuscaPaginacaoTest extends TestCase
{
    use RefreshDatabase;
    use CriaCenarioSensor;

    public function test_busca_filtra_a_listagem_pelo_nome(): void
    {
        [, $lavoura] = $this->cenarioComSensor(['ds_nome' => 'Sensor de Umidade']);
        $this->criarSensor($lavoura, ['ds_nome' => 'Sensor de pH']);

        $this->actingAs($lavoura->propriedade->usuario)
            ->get('/sensores?busca=Umidade')
            ->assertOk()
            ->assertSee('Sensor de Umidade')
            ->assertDontSee('Sensor de pH');
    }

    public function test_busca_sem_resultado_mostra_aviso_em_vez_do_estado_vazio_de_cadastro(): void
    {
        [, $lavoura] = $this->cenarioComSensor();

        $this->actingAs($lavoura->propriedade->usuario)
            ->get('/sensores?busca=inexistente')
            ->assertOk()
            ->assertSee('Nenhum resultado para', false)
            ->assertDontSee('Nenhum sensor cadastrado');
    }

    public function test_listagem_pagina_quando_passa_do_limite_por_pagina(): void
    {
        [$usuario, $lavoura] = $this->cenarioComSensor(['ds_nome' => 'Sensor 01']);

        foreach (range(2, 20) as $numero) {
            $this->criarSensor($lavoura, ['ds_nome' => 'Sensor ' . str_pad($numero, 2, '0', STR_PAD_LEFT)]);
        }

        $resposta = $this->actingAs($usuario)->get('/sensores')->assertOk();

        $paginador = $resposta->viewData('sensores');
        $this->assertSame(20, $paginador->total());
        $this->assertCount(15, $paginador->items());
        $this->assertTrue($paginador->hasMorePages());

        $this->actingAs($usuario)->get('/sensores?page=2')
            ->assertOk()
            ->assertSee('Sensor 20');
    }

    public function test_estatisticas_do_cabecalho_contam_o_total_e_nao_so_a_pagina(): void
    {
        [$usuario, $lavoura] = $this->cenarioComSensor();

        foreach (range(2, 20) as $numero) {
            $this->criarSensor($lavoura, ['ds_nome' => 'Sensor ' . $numero]);
        }

        $stats = $this->actingAs($usuario)->get('/sensores')->viewData('stats');

        $this->assertSame(20, $stats[0]['valor']);
    }

    public function test_listagem_carrega_a_ultima_leitura_junto_para_nao_consultar_por_linha(): void
    {
        [$usuario, $lavoura] = $this->cenarioComSensor();
        $this->criarSensor($lavoura, ['ds_nome' => 'Outro sensor']);

        $paginador = $this->actingAs($usuario)->get('/sensores')->viewData('sensores');

        foreach ($paginador->items() as $sensor) {
            /** @var Sensor $sensor */
            $this->assertTrue($sensor->relationLoaded('ultimaLeitura'));
            $this->assertTrue($sensor->relationLoaded('lavoura'));
        }
    }
}
