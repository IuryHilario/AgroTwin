<?php

namespace Tests\Feature;

use App\Support\FaixasSugeridas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioSensor;
use Tests\TestCase;

class LimitesLavouraTest extends TestCase
{
    use RefreshDatabase;
    use CriaCenarioSensor;

    public function test_salva_os_limites_da_lavoura(): void
    {
        [$usuario, $lavoura] = $this->cenarioComSensor();

        $this->actingAs($usuario)
            ->post("/lavouras/{$lavoura->id_lavoura}/limites", [
                'limites' => ['ph' => ['valor_min' => 5.5, 'valor_max' => 7.0]],
            ])
            ->assertRedirect(route('lavouras.index'));

        $this->assertDatabaseHas('configuracoes_limites', [
            'id_lavoura' => $lavoura->id_lavoura,
            'tp_sensor' => 'ph',
            'valor_min' => 5.5,
            'valor_max' => 7.0,
        ]);
    }

    public function test_recusa_minimo_maior_ou_igual_ao_maximo(): void
    {
        [$usuario, $lavoura] = $this->cenarioComSensor();

        $this->actingAs($usuario)
            ->post("/lavouras/{$lavoura->id_lavoura}/limites", [
                'limites' => ['ph' => ['valor_min' => 8, 'valor_max' => 6]],
            ])
            ->assertSessionHasErrors('limites.ph.valor_min');

        $this->assertDatabaseCount('configuracoes_limites', 0);
    }

    public function test_tela_de_limites_oferece_valores_de_referencia_da_cultura(): void
    {
        [$usuario, $lavoura] = $this->cenarioComSensor();
        $lavoura->update(['ds_cultura' => 'Alface crespa']);

        $this->actingAs($usuario)
            ->get("/lavouras/{$lavoura->id_lavoura}/limites")
            ->assertOk()
            // Abre já na cultura adivinhada e leva as faixas de referência para o JS.
            ->assertSee("cultura: 'alface'", false)
            ->assertSee('Preencher')
            ->assertSee('Cana-de-açúcar', false);

        // Os números em si vêm daqui e viajam para o JS pelo x-data da tela.
        $this->assertSame(['min' => 6.0, 'max' => 6.8], FaixasSugeridas::paraFormulario()['alface']['ph']);
        $this->assertSame(['min' => 60, 'max' => 80], FaixasSugeridas::paraFormulario()['alface']['umidade_solo']);
    }

    public function test_cultura_desconhecida_cai_na_referencia_padrao(): void
    {
        $this->assertSame('padrao', FaixasSugeridas::adivinhar('Quiabo'));
        $this->assertSame('milho', FaixasSugeridas::adivinhar('MILHO safrinha'));
        $this->assertArrayHasKey('umidade_solo', FaixasSugeridas::daCultura('padrao'));
    }
}
