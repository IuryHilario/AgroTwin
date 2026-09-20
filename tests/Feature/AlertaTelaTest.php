<?php

namespace Tests\Feature;

use App\Models\Alerta;
use App\Models\Lavoura;
use App\Models\Sensor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioSensor;
use Tests\TestCase;

class AlertaTelaTest extends TestCase
{
    use RefreshDatabase;
    use CriaCenarioSensor;

    private function criarAlerta(Sensor $sensor, Lavoura $lavoura, array $atributos = []): Alerta
    {
        return Alerta::create(array_merge([
            'id_sensor' => $sensor->id_sensor,
            'id_lavoura' => $lavoura->id_lavoura,
            'tp_severidade' => 'warning',
            'ds_mensagem' => 'Mensagem de teste',
            'fl_lida' => false,
            'dt_alerta' => now(),
        ], $atributos));
    }

    public function test_tela_abre_nos_nao_lidos(): void
    {
        [$usuario, $lavoura, $sensor] = $this->cenarioComSensor();
        $this->criarAlerta($sensor, $lavoura, ['ds_mensagem' => 'Alerta pendente']);
        $this->criarAlerta($sensor, $lavoura, ['ds_mensagem' => 'Alerta resolvido', 'fl_lida' => true]);

        $this->actingAs($usuario)
            ->get('/alertas')
            ->assertOk()
            ->assertSee('Alerta pendente')
            ->assertDontSee('Alerta resolvido');
    }

    public function test_filtro_de_severidade_restringe_a_lista(): void
    {
        [$usuario, $lavoura, $sensor] = $this->cenarioComSensor();
        $this->criarAlerta($sensor, $lavoura, ['ds_mensagem' => 'Desvio grande', 'tp_severidade' => 'critical']);
        $this->criarAlerta($sensor, $lavoura, ['ds_mensagem' => 'Desvio pequeno']);

        $this->actingAs($usuario)
            ->get('/alertas?severidade=critical')
            ->assertOk()
            ->assertSee('Desvio grande')
            ->assertDontSee('Desvio pequeno');
    }

    public function test_marcar_todos_como_lidos_afeta_so_os_alertas_do_usuario(): void
    {
        [$usuario, $lavoura, $sensor] = $this->cenarioComSensor();
        $meuAlerta = $this->criarAlerta($sensor, $lavoura);

        [, $lavouraVizinha, $sensorVizinho] = $this->cenarioComSensor();
        $alertaVizinho = $this->criarAlerta($sensorVizinho, $lavouraVizinha);

        $this->actingAs($usuario)
            ->postJson('/alertas/marcar-todos-lidos')
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertTrue($meuAlerta->refresh()->fl_lida);
        $this->assertFalse($alertaVizinho->refresh()->fl_lida);
    }

    public function test_intruso_nao_marca_alerta_de_outro_usuario_como_lido(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor();
        $alerta = $this->criarAlerta($sensor, $lavoura);
        $intruso = $this->criarUsuario();

        $this->actingAs($intruso)
            ->post("/alertas/{$alerta->id_alerta}/marcar-lido")
            ->assertNotFound();

        $this->assertFalse($alerta->refresh()->fl_lida);
    }
}
