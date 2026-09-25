<?php

namespace Tests\Feature;

use App\Models\Propriedade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\CriaCenarioSensor;
use Tests\TestCase;

class PropriedadeCadastroTest extends TestCase
{
    use RefreshDatabase;
    use CriaCenarioSensor;

    private function dadosValidos(array $sobrescrever = []): array
    {
        return array_merge([
            'ds_nome' => 'Fazenda Boa Vista',
            'nu_area_hectares' => '12.5',
            'tp_solo' => 'latosolo_vermelho',
            'ds_localizacao' => 'Rio Verde, Goiás, Brasil',
            'nu_latitude' => '-17.776903',
            'nu_longitude' => '-50.834688',
        ], $sobrescrever);
    }

    public function test_tela_de_cadastro_mostra_as_tres_etapas(): void
    {
        $this->actingAs($this->criarUsuario())
            ->get('/propriedade/inserir')
            ->assertOk()
            ->assertSee('stepper({ total: 3', false)
            ->assertSeeInOrder(['Propriedade', 'Localidade', 'Finalização'])
            ->assertSee('seletorLocalidade(', false)
            ->assertSee('previsaoClima(', false)
            ->assertSee('Cadastrar propriedade');
    }

    public function test_cadastra_a_propriedade_com_as_coordenadas(): void
    {
        $usuario = $this->criarUsuario();

        $this->actingAs($usuario)
            ->post('/propriedade', $this->dadosValidos())
            ->assertRedirect(route('propriedade.index'));

        $propriedade = Propriedade::firstWhere('ds_nome', 'Fazenda Boa Vista');
        $this->assertSame($usuario->id_usuario, $propriedade->id_usuario);
        $this->assertSame(-17.776903, $propriedade->nu_latitude);
        $this->assertSame(-50.834688, $propriedade->nu_longitude);
        $this->assertSame('Rio Verde, Goiás, Brasil', $propriedade->ds_localizacao);
    }

    public function test_exige_o_ponto_no_mapa_e_o_nome_do_local(): void
    {
        $this->actingAs($this->criarUsuario())
            ->postJson('/propriedade', $this->dadosValidos([
                'nu_latitude' => '',
                'nu_longitude' => '',
                'ds_localizacao' => '',
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nu_latitude', 'nu_longitude', 'ds_localizacao']);

        $this->assertDatabaseCount('propriedades', 0);
    }

    public function test_recusa_coordenadas_fora_do_intervalo(): void
    {
        $this->actingAs($this->criarUsuario())
            ->postJson('/propriedade', $this->dadosValidos(['nu_latitude' => '95', 'nu_longitude' => '-200']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nu_latitude', 'nu_longitude']);
    }

    public function test_recusa_tipo_de_solo_fora_da_lista(): void
    {
        $this->actingAs($this->criarUsuario())
            ->postJson('/propriedade', $this->dadosValidos(['tp_solo' => 'lua']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['tp_solo']);
    }

    public function test_edicao_abre_com_todas_as_etapas_liberadas(): void
    {
        $usuario = $this->criarUsuario();
        $propriedade = $this->criarPropriedade($usuario, $this->dadosValidos());

        $this->actingAs($usuario)
            ->get("/propriedade/{$propriedade->id_propriedade}/edit")
            ->assertOk()
            ->assertSee('navegacaoLivre: true', false)
            ->assertSee('Salvar alterações');
    }

    public function test_edicao_valida_os_dados_antes_de_gravar(): void
    {
        // Antes o update gravava $request->all() sem validação nenhuma.
        $usuario = $this->criarUsuario();
        $propriedade = $this->criarPropriedade($usuario, $this->dadosValidos());

        $this->actingAs($usuario)
            ->putJson("/propriedade/{$propriedade->id_propriedade}", $this->dadosValidos(['nu_area_hectares' => 'muita']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nu_area_hectares']);

        $this->assertSame(12.5, (float) $propriedade->refresh()->nu_area_hectares);
    }

    public function test_edicao_grava_o_novo_ponto_e_ignora_campos_fora_do_formulario(): void
    {
        $usuario = $this->criarUsuario();
        $outro = $this->criarUsuario();
        $propriedade = $this->criarPropriedade($usuario, $this->dadosValidos());

        $this->actingAs($usuario)
            ->put("/propriedade/{$propriedade->id_propriedade}", $this->dadosValidos([
                'nu_latitude' => '-16.678610',
                'nu_longitude' => '-49.253890',
                'ds_localizacao' => 'Goiânia, Goiás, Brasil',
                // Tentativas de mexer no que o formulário não expõe:
                'id_usuario' => $outro->id_usuario,
                'fl_inativo' => '1',
            ]))
            ->assertRedirect(route('propriedade.index'));

        $propriedade->refresh();
        $this->assertSame(-16.67861, $propriedade->nu_latitude);
        $this->assertSame($usuario->id_usuario, $propriedade->id_usuario);
        $this->assertFalse($propriedade->fl_inativo);
    }

    // --- Endpoints do seletor de localidade ------------------------------

    public function test_busca_de_municipio_exige_login(): void
    {
        $this->getJson('/localidades/buscar?q=Rio%20Verde')->assertUnauthorized();
    }

    public function test_busca_de_municipio_sinaliza_indisponibilidade(): void
    {
        Http::fake(['geocoding-api.open-meteo.com/*' => Http::response('erro', 500)]);

        $this->actingAs($this->criarUsuario())
            ->getJson('/localidades/buscar?q=Rio%20Verde')
            ->assertOk()
            ->assertExactJson(['resultados' => [], 'indisponivel' => true]);
    }

    public function test_busca_de_municipio_devolve_resultados(): void
    {
        Http::fake(['geocoding-api.open-meteo.com/*' => Http::response(['results' => [
            ['name' => 'Goiânia', 'latitude' => -16.67861, 'longitude' => -49.25389, 'admin1' => 'Goiás', 'country' => 'Brasil'],
        ]])]);

        $this->actingAs($this->criarUsuario())
            ->getJson('/localidades/buscar?q=Goiania')
            ->assertOk()
            ->assertJsonPath('indisponivel', false)
            ->assertJsonPath('resultados.0.rotulo', 'Goiânia, Goiás, Brasil');
    }

    public function test_previa_do_clima_valida_as_coordenadas(): void
    {
        $this->actingAs($this->criarUsuario())
            ->getJson('/localidades/clima?latitude=120&longitude=0')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['latitude']);
    }
}
