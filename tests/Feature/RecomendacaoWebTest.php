<?php

namespace Tests\Feature;

use App\Models\Recomendacao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioSensor;
use Tests\TestCase;

class RecomendacaoWebTest extends TestCase
{
    use RefreshDatabase;
    use CriaCenarioSensor;

    public function test_lista_apenas_recomendacoes_do_usuario_logado(): void
    {
        [$usuario, $lavoura] = $this->cenarioComSensor();
        $recomendacao = Recomendacao::create([
            'id_lavoura' => $lavoura->id_lavoura,
            'tp_sensor' => 'ph',
            'ds_recomendacao' => 'pH do solo baixo (ácido) — considere aplicar calcário.',
            'nu_valor_leitura' => 9.0,
            'nu_limite_min' => 5.5,
            'nu_limite_max' => 7.0,
            'dt_recomendacao' => now(),
        ]);

        [, $outraLavoura] = $this->cenarioComSensor();
        Recomendacao::create([
            'id_lavoura' => $outraLavoura->id_lavoura,
            'tp_sensor' => 'ph',
            'ds_recomendacao' => 'Recomendação de outro usuário',
            'dt_recomendacao' => now(),
        ]);

        $response = $this->actingAs($usuario)->get('/recomendacoes');

        $response->assertOk();
        $response->assertViewHas('recomendacoes', function ($recomendacoes) use ($recomendacao) {
            return $recomendacoes->count() === 1
                && $recomendacoes->first()->id_recomendacao === $recomendacao->id_recomendacao;
        });
    }

    public function test_detalhar_mostra_o_motivo_da_recomendacao(): void
    {
        [$usuario, $lavoura] = $this->cenarioComSensor();
        $recomendacao = Recomendacao::create([
            'id_lavoura' => $lavoura->id_lavoura,
            'tp_sensor' => 'ph',
            'ds_recomendacao' => 'pH do solo baixo (ácido) — considere aplicar calcário.',
            'nu_valor_leitura' => 4.5,
            'nu_limite_min' => 5.5,
            'nu_limite_max' => 7.0,
            'dt_recomendacao' => now(),
        ]);

        $response = $this->actingAs($usuario)->get("/recomendacoes/{$recomendacao->id_recomendacao}");

        $response->assertOk();
        $response->assertSee('abaixo do mínimo configurado', false);
    }

    public function test_nao_pode_ver_detalhe_de_recomendacao_de_outro_usuario(): void
    {
        [, $lavoura] = $this->cenarioComSensor();
        $recomendacao = Recomendacao::create([
            'id_lavoura' => $lavoura->id_lavoura,
            'tp_sensor' => 'ph',
            'ds_recomendacao' => 'Texto qualquer',
            'dt_recomendacao' => now(),
        ]);

        $outroUsuario = $this->criarUsuario();

        $response = $this->actingAs($outroUsuario)->get("/recomendacoes/{$recomendacao->id_recomendacao}");

        $response->assertStatus(404);
    }
}
