<?php

namespace Tests\Feature;

use App\Services\RecomendacaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioSensor;
use Tests\TestCase;

class RecomendacaoServiceTest extends TestCase
{
    use RefreshDatabase;
    use CriaCenarioSensor;

    public function test_registra_recomendacao_quando_valor_fora_do_limite(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor(['tp_sensor' => 'ph']);
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);

        $recomendacao = app(RecomendacaoService::class)->avaliarERegistrar($sensor, 9.0);

        $this->assertNotNull($recomendacao);
        $this->assertDatabaseHas('recomendacoes', [
            'id_lavoura' => $lavoura->id_lavoura,
            'tp_sensor' => 'ph',
        ]);
    }

    public function test_nao_registra_recomendacao_quando_valor_dentro_do_limite(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor(['tp_sensor' => 'ph']);
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);

        $recomendacao = app(RecomendacaoService::class)->avaliarERegistrar($sensor, 6.5);

        $this->assertNull($recomendacao);
        $this->assertDatabaseCount('recomendacoes', 0);
    }

    public function test_nao_registra_recomendacao_sem_limite_configurado(): void
    {
        [, , $sensor] = $this->cenarioComSensor(['tp_sensor' => 'ph']);

        $recomendacao = app(RecomendacaoService::class)->avaliarERegistrar($sensor, 9.0);

        $this->assertNull($recomendacao);
    }
}
