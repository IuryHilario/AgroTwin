<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioSensor;
use Tests\TestCase;

class PropriedadeInativarTest extends TestCase
{
    use RefreshDatabase;
    use CriaCenarioSensor;

    public function test_agricultor_pode_inativar_e_ativar_propriedade(): void
    {
        $usuario = $this->criarUsuario();
        $propriedade = $this->criarPropriedade($usuario);

        $response = $this->actingAs($usuario)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->getJson("/propriedade/{$propriedade->id_propriedade}/inativar");

        $response->assertOk();
        $response->assertJson(['success' => true, 'message' => 'Inativado com Sucesso!!']);
        $this->assertTrue($propriedade->refresh()->fl_inativo);

        $response = $this->actingAs($usuario)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->getJson("/propriedade/{$propriedade->id_propriedade}/inativar");

        $response->assertJson(['success' => true, 'message' => 'Ativado com Sucesso!!']);
        $this->assertFalse($propriedade->refresh()->fl_inativo);
    }

    public function test_agricultor_nao_pode_inativar_propriedade_de_outro_usuario(): void
    {
        $usuario = $this->criarUsuario();
        $propriedade = $this->criarPropriedade($usuario);
        $outroUsuario = $this->criarUsuario();

        $response = $this->actingAs($outroUsuario)->get("/propriedade/{$propriedade->id_propriedade}/inativar");

        $response->assertStatus(404);
        $this->assertFalse($propriedade->refresh()->fl_inativo);
    }

    public function test_propriedade_inativa_nao_aparece_no_seletor_do_dashboard(): void
    {
        $usuario = $this->criarUsuario();
        $ativa = $this->criarPropriedade($usuario, ['ds_nome' => 'Ativa']);
        $inativa = $this->criarPropriedade($usuario, ['ds_nome' => 'Inativa']);
        $inativa->update(['fl_inativo' => true]);

        $response = $this->actingAs($usuario)->get('/dashboard');

        $response->assertOk();
        $response->assertViewHas('propriedades', function ($propriedades) use ($ativa, $inativa) {
            return $propriedades->contains('id_propriedade', $ativa->id_propriedade)
                && ! $propriedades->contains('id_propriedade', $inativa->id_propriedade);
        });
    }
}
