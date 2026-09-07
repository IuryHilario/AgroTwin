<?php

namespace Tests\Feature;

use App\Models\Insumo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioSensor;
use Tests\TestCase;

/**
 * O componente <x-ui.responsive-table-card> mostra no máximo 3 botões de
 * ação por linha; a partir do 4º, agrupa o restante atrás de um botão "..."
 * (menu "Mais ações"). Insumos tem 5 ações (detalhar, editar, estoque,
 * aplicação, relatório) — cenário real que exercita o agrupamento.
 */
class ResponsiveTableActionsMenuTest extends TestCase
{
    use RefreshDatabase;
    use CriaCenarioSensor;

    public function test_mostra_menu_de_mais_acoes_quando_ha_mais_de_tres_botoes(): void
    {
        $usuario = $this->criarUsuario();
        (new Insumo())->inserir([
            'ds_nome' => 'Ureia',
            'tp_insumo' => 'fertilizante',
        ], $usuario->id_usuario);

        $response = $this->actingAs($usuario)->get('/insumos');

        $response->assertOk();
        $response->assertSee('Mais ações');
        $response->assertSee('fa-ellipsis-vertical', false);
    }

    public function test_nao_mostra_menu_de_mais_acoes_quando_ha_ate_tres_botoes(): void
    {
        $usuario = $this->criarUsuario();
        $this->criarPropriedade($usuario);

        $response = $this->actingAs($usuario)->get('/propriedade');

        $response->assertOk();
        $response->assertDontSee('Mais ações');
    }
}
