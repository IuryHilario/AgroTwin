<?php

namespace Tests\Feature;

use App\Services\IrrigacaoService;
use App\Services\SensorReadingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioSensor;
use Tests\TestCase;

class IrrigacaoServiceTest extends TestCase
{
    use RefreshDatabase;
    use CriaCenarioSensor;

    private function registrarLeitura($sensor, float $valor)
    {
        return app(SensorReadingService::class)->registrar($sensor, $valor);
    }

    public function test_inicia_irrigacao_quando_umidade_abaixo_do_minimo(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor(['tp_sensor' => 'umidade_solo']);
        $this->definirLimite($lavoura, 'umidade_solo', 30, 80);

        $leitura = $this->registrarLeitura($sensor, 18.0);
        app(IrrigacaoService::class)->verificar($leitura);

        $lavoura->refresh();
        $this->assertTrue($lavoura->fl_irrigacao_ativa);

        $this->assertDatabaseHas('historico_irrigacao', [
            'id_lavoura' => $lavoura->id_lavoura,
            'tp_acionamento' => 'automatico',
            'dt_fim' => null,
        ]);
    }

    public function test_nao_inicia_irrigacao_quando_umidade_dentro_do_limite(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor(['tp_sensor' => 'umidade_solo']);
        $this->definirLimite($lavoura, 'umidade_solo', 30, 80);

        $leitura = $this->registrarLeitura($sensor, 50.0);
        app(IrrigacaoService::class)->verificar($leitura);

        $this->assertFalse($lavoura->refresh()->fl_irrigacao_ativa);
        $this->assertDatabaseCount('historico_irrigacao', 0);
    }

    public function test_encerra_irrigacao_automaticamente_quando_umidade_normaliza(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor(['tp_sensor' => 'umidade_solo']);
        $this->definirLimite($lavoura, 'umidade_solo', 30, 80);

        app(IrrigacaoService::class)->verificar($this->registrarLeitura($sensor, 18.0));
        $this->assertTrue($lavoura->refresh()->fl_irrigacao_ativa);

        app(IrrigacaoService::class)->verificar($this->registrarLeitura($sensor, 45.0));

        $this->assertFalse($lavoura->refresh()->fl_irrigacao_ativa);
        $this->assertDatabaseMissing('historico_irrigacao', [
            'id_lavoura' => $lavoura->id_lavoura,
            'dt_fim' => null,
        ]);
    }

    public function test_ignora_leituras_de_outros_parametros(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor(['tp_sensor' => 'ph']);
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);

        $leitura = $this->registrarLeitura($sensor, 9.0);
        app(IrrigacaoService::class)->verificar($leitura);

        $this->assertFalse($lavoura->refresh()->fl_irrigacao_ativa);
    }

    public function test_iniciar_e_encerrar_manualmente(): void
    {
        [, $lavoura] = $this->cenarioComSensor();
        $service = app(IrrigacaoService::class);

        $service->iniciar($lavoura, 'manual', 'Teste manual');
        $this->assertTrue($lavoura->refresh()->fl_irrigacao_ativa);
        $this->assertDatabaseHas('historico_irrigacao', [
            'id_lavoura' => $lavoura->id_lavoura,
            'tp_acionamento' => 'manual',
        ]);

        $service->encerrar($lavoura);
        $this->assertFalse($lavoura->refresh()->fl_irrigacao_ativa);
        $this->assertDatabaseMissing('historico_irrigacao', [
            'id_lavoura' => $lavoura->id_lavoura,
            'dt_fim' => null,
        ]);
    }
}
