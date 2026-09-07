<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioSensor;
use Tests\TestCase;

class LavouraDestroyTest extends TestCase
{
    use RefreshDatabase;
    use CriaCenarioSensor;

    public function test_agricultor_pode_excluir_lavoura(): void
    {
        [$usuario, $lavoura] = $this->cenarioComSensor();

        $response = $this->actingAs($usuario)->delete("/lavouras/{$lavoura->id_lavoura}");

        $response->assertRedirect(route('lavouras.index'));
        $this->assertDatabaseMissing('lavouras', ['id_lavoura' => $lavoura->id_lavoura]);
    }

    public function test_exclusao_via_ajax_retorna_json(): void
    {
        [$usuario, $lavoura] = $this->cenarioComSensor();

        $response = $this->actingAs($usuario)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->deleteJson("/lavouras/{$lavoura->id_lavoura}");

        $response->assertOk();
        $response->assertJson(['success' => true, 'message' => 'Excluído com Sucesso!!']);
        $this->assertDatabaseMissing('lavouras', ['id_lavoura' => $lavoura->id_lavoura]);
    }

    public function test_agricultor_nao_pode_excluir_lavoura_de_outro_usuario(): void
    {
        [, $lavoura] = $this->cenarioComSensor();
        $outroUsuario = $this->criarUsuario();

        $response = $this->actingAs($outroUsuario)->delete("/lavouras/{$lavoura->id_lavoura}");

        $response->assertStatus(404);
        $this->assertDatabaseHas('lavouras', ['id_lavoura' => $lavoura->id_lavoura]);
    }
}
