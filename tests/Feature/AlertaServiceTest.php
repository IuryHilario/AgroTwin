<?php

namespace Tests\Feature;

use App\Mail\AlertaGeradoMail;
use App\Models\Alerta;
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

    public function test_desvio_pequeno_gera_alerta_de_atencao(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor();
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);

        // 0,15 fora numa faixa de 1,5 de largura: 10% de desvio.
        $alerta = app(AlertaService::class)->verificar($this->registrarLeitura($sensor, 7.15));

        $this->assertSame('warning', $alerta->tp_severidade);
    }

    public function test_desvio_grande_gera_alerta_critico(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor();
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);

        // 1,0 fora numa faixa de 1,5 de largura: 67% de desvio.
        $alerta = app(AlertaService::class)->verificar($this->registrarLeitura($sensor, 4.5));

        $this->assertSame('critical', $alerta->tp_severidade);
    }

    public function test_desvio_que_continua_nao_gera_um_alerta_por_leitura(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor();
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);
        $servico = app(AlertaService::class);

        foreach ([4.0, 4.1, 3.9, 4.2] as $valor) {
            $servico->verificar($this->registrarLeitura($sensor, $valor));
        }

        $this->assertDatabaseCount('alertas', 1);

        $alerta = Alerta::first();
        $this->assertSame(4, $alerta->nu_ocorrencias);
        $this->assertTrue($alerta->emCurso());
        // A mensagem acompanha o valor mais recente.
        $this->assertStringContainsString('4.2', $alerta->ds_mensagem);
    }

    public function test_email_sai_uma_vez_por_episodio_e_nao_por_leitura(): void
    {
        Mail::fake();

        [$usuario, $lavoura, $sensor] = $this->cenarioComSensor();
        $usuario->update(['fl_notificar_email_alerta' => true]);
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);
        $servico = app(AlertaService::class);

        foreach ([4.0, 4.1, 3.9] as $valor) {
            $servico->verificar($this->registrarLeitura($sensor, $valor));
        }

        Mail::assertQueuedCount(1);
    }

    public function test_valor_de_volta_a_faixa_encerra_o_episodio(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor();
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);
        $servico = app(AlertaService::class);

        $servico->verificar($this->registrarLeitura($sensor, 4.0));
        $servico->verificar($this->registrarLeitura($sensor, 6.2));

        $this->assertFalse(Alerta::first()->emCurso());

        // Desvio novo depois de normalizar abre outro alerta.
        $servico->verificar($this->registrarLeitura($sensor, 4.0));
        $this->assertDatabaseCount('alertas', 2);
    }

    public function test_valor_na_borda_da_faixa_nao_encerra_o_episodio(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor();
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);
        $servico = app(AlertaService::class);

        $servico->verificar($this->registrarLeitura($sensor, 5.0));
        // Dentro da faixa, mas a 0,02 do mínimo: sem a histerese isso encerraria
        // o episódio e a próxima leitura abriria outro, com novo e-mail.
        $servico->verificar($this->registrarLeitura($sensor, 5.52));

        $this->assertTrue(Alerta::first()->emCurso());
    }

    public function test_severidade_do_episodio_nao_regride(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor();
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);
        $servico = app(AlertaService::class);

        $servico->verificar($this->registrarLeitura($sensor, 4.5));
        $this->assertSame('critical', Alerta::first()->tp_severidade);

        $servico->verificar($this->registrarLeitura($sensor, 5.4));
        $this->assertSame('critical', Alerta::first()->tp_severidade);
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
