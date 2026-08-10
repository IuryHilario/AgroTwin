<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioSensor;
use Tests\TestCase;

class LavouraIrrigacaoWebTest extends TestCase
{
    use RefreshDatabase;
    use CriaCenarioSensor;

    public function test_agricultor_pode_iniciar_irrigacao_manualmente(): void
    {
        [$usuario, $lavoura] = $this->cenarioComSensor();

        $response = $this->actingAs($usuario)->get("/lavouras/{$lavoura->id_lavoura}/irrigacao/iniciar");

        $response->assertRedirect();
        $this->assertTrue($lavoura->refresh()->fl_irrigacao_ativa);
        $this->assertDatabaseHas('historico_irrigacao', [
            'id_lavoura' => $lavoura->id_lavoura,
            'tp_acionamento' => 'manual',
        ]);
    }

    public function test_agricultor_pode_parar_irrigacao_manualmente(): void
    {
        [$usuario, $lavoura] = $this->cenarioComSensor();
        $lavoura->update(['fl_irrigacao_ativa' => true]);

        $response = $this->actingAs($usuario)->get("/lavouras/{$lavoura->id_lavoura}/irrigacao/parar");

        $response->assertRedirect();
        $this->assertFalse($lavoura->refresh()->fl_irrigacao_ativa);
    }

    public function test_agricultor_nao_pode_controlar_irrigacao_de_lavoura_de_outro_usuario(): void
    {
        [, $lavoura] = $this->cenarioComSensor();
        $outroUsuario = $this->criarUsuario();

        $response = $this->actingAs($outroUsuario)->get("/lavouras/{$lavoura->id_lavoura}/irrigacao/iniciar");

        $response->assertStatus(404);
        $this->assertFalse($lavoura->refresh()->fl_irrigacao_ativa);
    }
}
