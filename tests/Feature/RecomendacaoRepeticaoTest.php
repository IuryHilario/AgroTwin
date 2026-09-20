<?php

namespace Tests\Feature;

use App\Models\Recomendacao;
use App\Services\RecomendacaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioSensor;
use Tests\TestCase;

class RecomendacaoRepeticaoTest extends TestCase
{
    use RefreshDatabase;
    use CriaCenarioSensor;

    public function test_nao_registra_a_mesma_recomendacao_a_cada_leitura(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor();
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);
        $servico = app(RecomendacaoService::class);

        foreach ([4.0, 4.1, 3.9, 4.2] as $valor) {
            $servico->avaliarERegistrar($sensor, $valor);
        }

        $this->assertDatabaseCount('recomendacoes', 1);
    }

    public function test_registra_de_novo_depois_da_janela(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor();
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);
        $servico = app(RecomendacaoService::class);

        $servico->avaliarERegistrar($sensor, 4.0);
        Recomendacao::query()->update(['dt_recomendacao' => now()->subHours(13)]);
        $servico->avaliarERegistrar($sensor, 4.0);

        $this->assertDatabaseCount('recomendacoes', 2);
    }

    public function test_desvio_no_outro_sentido_gera_recomendacao_diferente(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor();
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);
        $servico = app(RecomendacaoService::class);

        $servico->avaliarERegistrar($sensor, 4.0);
        $servico->avaliarERegistrar($sensor, 9.0);

        $this->assertDatabaseCount('recomendacoes', 2);
    }
}
