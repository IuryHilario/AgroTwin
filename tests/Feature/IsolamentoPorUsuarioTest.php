<?php

namespace Tests\Feature;

use App\Models\Insumo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioSensor;
use Tests\TestCase;

/**
 * Multi-tenant: um produtor logado não pode ver nem alterar registro de outro.
 *
 * Estas telas usavam `Model::findOrFail($id)` sem filtro de dono — bastava
 * trocar o id na URL. No caso do sensor isso expunha o token do ESP32.
 */
class IsolamentoPorUsuarioTest extends TestCase
{
    use RefreshDatabase;
    use CriaCenarioSensor;

    /** Dois produtores: o dono do cenário e um intruso autenticado. */
    private function cenarioComIntruso(): array
    {
        [$dono, $lavoura, $sensor] = $this->cenarioComSensor(['ds_nome' => 'Sensor do vizinho']);

        return [$dono, $lavoura, $sensor, $this->criarUsuario()];
    }

    public function test_intruso_nao_abre_a_edicao_do_sensor_nem_ve_o_token(): void
    {
        [, , $sensor, $intruso] = $this->cenarioComIntruso();

        $this->actingAs($intruso)
            ->get("/sensores/{$sensor->id_sensor}/edit")
            ->assertNotFound();
    }

    public function test_intruso_nao_ve_os_detalhes_do_sensor(): void
    {
        [, , $sensor, $intruso] = $this->cenarioComIntruso();

        $this->actingAs($intruso)
            ->get("/sensores/{$sensor->id_sensor}/show")
            ->assertNotFound();
    }

    public function test_intruso_nao_altera_sensor_de_outro_usuario(): void
    {
        [, , $sensor, $intruso] = $this->cenarioComIntruso();

        $this->actingAs($intruso)
            ->put("/sensores/{$sensor->id_sensor}/update", [
                'ds_nome' => 'Invadido',
                'tp_sensor' => 'ph',
                'ds_status' => 'ativo',
                'id_propriedade' => $sensor->id_propriedade,
                'id_lavoura' => $sensor->id_lavoura,
            ])
            ->assertNotFound();

        $this->assertSame('Sensor do vizinho', $sensor->refresh()->ds_nome);
    }

    public function test_intruso_nao_exclui_sensor_de_outro_usuario(): void
    {
        [, , $sensor, $intruso] = $this->cenarioComIntruso();

        $this->actingAs($intruso)
            ->delete("/sensores/{$sensor->id_sensor}")
            ->assertNotFound();

        $this->assertDatabaseHas('sensores', ['id_sensor' => $sensor->id_sensor]);
    }

    public function test_intruso_nao_abre_a_edicao_da_lavoura(): void
    {
        [, $lavoura, , $intruso] = $this->cenarioComIntruso();

        $this->actingAs($intruso)
            ->get("/lavouras/{$lavoura->id_lavoura}/edit")
            ->assertNotFound();
    }

    public function test_intruso_nao_altera_lavoura_de_outro_usuario(): void
    {
        [, $lavoura, , $intruso] = $this->cenarioComIntruso();

        $this->actingAs($intruso)
            ->put("/lavouras/{$lavoura->id_lavoura}/update", [
                'ds_cultura' => 'Invadida',
                'id_propriedade' => $lavoura->id_propriedade,
            ])
            ->assertNotFound();

        $this->assertSame('Lavoura Teste', $lavoura->refresh()->ds_cultura);
    }

    public function test_intruso_nao_configura_limites_de_lavoura_de_outro_usuario(): void
    {
        [, $lavoura, , $intruso] = $this->cenarioComIntruso();

        $this->actingAs($intruso)
            ->get("/lavouras/{$lavoura->id_lavoura}/limites")
            ->assertNotFound();

        $this->actingAs($intruso)
            ->post("/lavouras/{$lavoura->id_lavoura}/limites", [
                'limites' => ['ph' => ['valor_min' => 1, 'valor_max' => 2]],
            ])
            ->assertNotFound();

        $this->assertDatabaseCount('configuracoes_limites', 0);
    }

    public function test_intruso_nao_monitora_lavoura_de_outro_usuario(): void
    {
        [, $lavoura, , $intruso] = $this->cenarioComIntruso();

        $this->actingAs($intruso)
            ->get("/lavouras/{$lavoura->id_lavoura}/monitorar")
            ->assertNotFound();
    }

    public function test_intruso_nao_abre_nem_altera_insumo_de_outro_usuario(): void
    {
        $dono = $this->criarUsuario();
        $intruso = $this->criarUsuario();
        $insumo = (new Insumo())->inserir([
            'ds_nome' => 'Ureia do vizinho',
            'tp_insumo' => 'fertilizante',
            'tp_unidade_medida' => 'kg',
        ], $dono->id_usuario);

        $this->actingAs($intruso)->get("/insumos/{$insumo->id_insumo}/edit")->assertNotFound();
        $this->actingAs($intruso)->get("/insumos/{$insumo->id_insumo}/estoque")->assertNotFound();
        $this->actingAs($intruso)->delete("/insumos/{$insumo->id_insumo}")->assertNotFound();

        $this->assertDatabaseHas('insumos', ['id_insumo' => $insumo->id_insumo]);
    }

    public function test_listagens_mostram_apenas_os_registros_do_usuario(): void
    {
        [, , $sensor, $intruso] = $this->cenarioComIntruso();

        $this->actingAs($intruso)
            ->get('/sensores')
            ->assertOk()
            ->assertDontSee($sensor->ds_nome)
            ->assertDontSee($sensor->token);
    }
}
