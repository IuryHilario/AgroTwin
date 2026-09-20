<?php

namespace Tests\Feature;

use App\Models\Lavoura;
use App\Models\Sensor;
use App\Services\DiagnosticoSoloService;
use App\Services\SensorReadingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioSensor;
use Tests\TestCase;

class DiagnosticoSoloServiceTest extends TestCase
{
    use RefreshDatabase;
    use CriaCenarioSensor;

    /**
     * Registra uma leitura por dia, do mais antigo para o mais recente.
     *
     * @param  array<int, float>  $valores
     */
    private function registrar(Sensor $sensor, array $valores): void
    {
        $servico = app(SensorReadingService::class);
        $dias = count($valores);

        foreach (array_values($valores) as $indice => $valor) {
            $servico->registrar($sensor, $valor, now()->subDays($dias - $indice));
        }
    }

    private function avaliar(Lavoura $lavoura, Sensor $sensor): array
    {
        $relatorio = app(SensorReadingService::class)
            ->relatorioPorSensores(collect([$sensor]), now()->subDays(30), now());

        return app(DiagnosticoSoloService::class)->avaliar($relatorio, $lavoura);
    }

    public function test_marca_como_ideal_quando_quase_tudo_esta_dentro_da_faixa(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor();
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);
        $this->registrar($sensor, [6.0, 6.2, 6.4, 6.1, 6.3]);

        $resultado = $this->avaliar($lavoura, $sensor);
        $parametro = $resultado['parametros'][0];

        $this->assertSame('ideal', $parametro['situacao']);
        $this->assertSame(100, $parametro['percentualDentro']);
        $this->assertSame(100, $resultado['indice']);
        $this->assertNull($parametro['acao']);
        $this->assertTrue($resultado['acoes']->isEmpty());
    }

    public function test_marca_como_critico_e_sugere_acao_quando_valor_fica_abaixo_do_minimo(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor();
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);
        $this->registrar($sensor, [4.0, 4.2, 4.1, 4.3, 6.0]);

        $resultado = $this->avaliar($lavoura, $sensor);
        $parametro = $resultado['parametros'][0];

        $this->assertSame('critico', $parametro['situacao']);
        $this->assertSame('abaixo', $parametro['direcao']);
        $this->assertSame(20, $parametro['percentualDentro']);
        $this->assertStringContainsString('calagem', $parametro['acao']);
        $this->assertSame('critico', $resultado['situacaoGeral']['nivel']);
        $this->assertCount(1, $resultado['acoes']);
    }

    public function test_sem_faixa_configurada_vira_uma_unica_acao_de_configuracao(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor();
        $outro = $this->criarSensor($lavoura, ['ds_nome' => 'Umidade', 'tp_sensor' => 'umidade_solo']);
        $this->registrar($sensor, [6.0, 6.2]);
        $this->registrar($outro, [40.0, 42.0]);

        $relatorio = app(SensorReadingService::class)
            ->relatorioPorSensores(collect([$sensor, $outro]), now()->subDays(30), now());
        $resultado = app(DiagnosticoSoloService::class)->avaliar($relatorio, $lavoura);

        $this->assertNull($resultado['indice']);
        $this->assertSame('neutro', $resultado['situacaoGeral']['nivel']);
        $this->assertCount(1, $resultado['acoes']);
        $this->assertSame('sem_limite', $resultado['acoes'][0]['situacao']);
        $this->assertStringContainsString('pH', $resultado['acoes'][0]['texto']);
        $this->assertStringContainsString('Umidade do Solo', $resultado['acoes'][0]['texto']);
    }

    public function test_detecta_tendencia_de_queda_ao_longo_do_periodo(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor();
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);
        $this->registrar($sensor, [7.0, 7.0, 7.0, 5.0, 5.0, 5.0]);

        $parametro = $this->avaliar($lavoura, $sensor)['parametros'][0];

        $this->assertSame('caindo', $parametro['tendencia']['sentido']);
        $this->assertLessThan(0, $parametro['tendencia']['variacao']);
    }

    public function test_sensor_sem_leitura_aparece_como_sem_dados(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor();
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);

        $resultado = $this->avaliar($lavoura, $sensor);

        $this->assertSame('sem_dados', $resultado['parametros'][0]['situacao']);
        $this->assertSame('sem_dados', $resultado['acoes'][0]['situacao']);
    }
}
