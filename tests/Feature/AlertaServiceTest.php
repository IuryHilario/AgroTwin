<?php

namespace Tests\Feature;

use App\Mail\AlertaGeradoMail;
use App\Services\AlertaService;
use App\Services\SensorReadingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\CriaCenarioSensor;
use Tests\TestCase;

class AlertaServiceTest extends TestCase
{
    use RefreshDatabase;
    use CriaCenarioSensor;

    private function registrarLeitura($sensor, float $valor)
    {
        return app(SensorReadingService::class)->registrar($sensor, $valor);
    }

    public function test_nao_gera_alerta_para_sensor_sem_lavoura(): void
    {
        $usuario = $this->criarUsuario();
        $propriedade = $this->criarPropriedade($usuario);
        $sensor = $this->criarSensor(
            $this->criarLavoura($propriedade),
            ['id_lavoura' => null]
        );

        $leitura = $this->registrarLeitura($sensor, 9.0);
        $alerta = app(AlertaService::class)->verificar($leitura);

        $this->assertNull($alerta);
        $this->assertDatabaseCount('alertas', 0);
    }

    public function test_nao_gera_alerta_sem_limite_configurado(): void
    {
        [, , $sensor] = $this->cenarioComSensor();

        $leitura = $this->registrarLeitura($sensor, 9.0);
        $alerta = app(AlertaService::class)->verificar($leitura);

        $this->assertNull($alerta);
    }

    public function test_nao_gera_alerta_quando_valor_dentro_do_limite(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor();
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);

        $leitura = $this->registrarLeitura($sensor, 6.5);
        $alerta = app(AlertaService::class)->verificar($leitura);

        $this->assertNull($alerta);
    }

    public function test_gera_alerta_quando_valor_abaixo_do_minimo(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor();
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);

        $leitura = $this->registrarLeitura($sensor, 4.0);
        $alerta = app(AlertaService::class)->verificar($leitura);

        $this->assertNotNull($alerta);
        $this->assertStringContainsString('abaixo do limite mínimo', $alerta->ds_mensagem);
        $this->assertFalse($alerta->fl_lida);
    }

    public function test_gera_alerta_quando_valor_acima_do_maximo(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor();
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);

        $leitura = $this->registrarLeitura($sensor, 9.0);
        $alerta = app(AlertaService::class)->verificar($leitura);

        $this->assertNotNull($alerta);
        $this->assertStringContainsString('acima do limite máximo', $alerta->ds_mensagem);
    }

    public function test_envia_email_quando_usuario_tem_notificacao_ativada(): void
    {
        Mail::fake();

        [$usuario, $lavoura, $sensor] = $this->cenarioComSensor();
        $usuario->fl_notificar_email_alerta = true;
        $usuario->save();
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);

        $leitura = $this->registrarLeitura($sensor, 9.0);
        app(AlertaService::class)->verificar($leitura);

        Mail::assertQueued(AlertaGeradoMail::class, fn ($mail) => $mail->hasTo($usuario->email));
    }

    public function test_nao_envia_email_quando_usuario_desativou_notificacao(): void
    {
        Mail::fake();

        [$usuario, $lavoura, $sensor] = $this->cenarioComSensor();
        $usuario->fl_notificar_email_alerta = false;
        $usuario->save();
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);

        $leitura = $this->registrarLeitura($sensor, 9.0);
        app(AlertaService::class)->verificar($leitura);

        Mail::assertNothingQueued();
    }
}
