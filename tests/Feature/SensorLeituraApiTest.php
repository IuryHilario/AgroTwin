<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\CriaCenarioSensor;
use Tests\TestCase;

class SensorLeituraApiTest extends TestCase
{
    use RefreshDatabase;
    use CriaCenarioSensor;

    public function test_recusa_requisicao_sem_token(): void
    {
        [, , $sensor] = $this->cenarioComSensor();

        $response = $this->postJson("/api/sensores/{$sensor->id_sensor}/leituras", ['valor' => 6.5]);

        $response->assertStatus(401);
    }

    public function test_recusa_requisicao_com_token_errado(): void
    {
        [, , $sensor] = $this->cenarioComSensor();

        $response = $this->withHeader('Authorization', 'Bearer token-errado')
            ->postJson("/api/sensores/{$sensor->id_sensor}/leituras", ['valor' => 6.5]);

        $response->assertStatus(401);
    }

    public function test_aceita_leitura_com_token_correto_e_persiste(): void
    {
        [, , $sensor] = $this->cenarioComSensor();

        $response = $this->withHeader('Authorization', 'Bearer ' . $sensor->token)
            ->postJson("/api/sensores/{$sensor->id_sensor}/leituras", ['valor' => 6.5]);

        $response->assertStatus(201)->assertJson(['success' => true]);

        $this->assertDatabaseHas('leituras_sensor', [
            'id_sensor' => $sensor->id_sensor,
            'valor' => 6.5,
        ]);
    }

    public function test_recusa_valor_nao_numerico(): void
    {
        [, , $sensor] = $this->cenarioComSensor();

        $response = $this->withHeader('Authorization', 'Bearer ' . $sensor->token)
            ->postJson("/api/sensores/{$sensor->id_sensor}/leituras", ['valor' => 'abc']);

        $response->assertStatus(422)->assertJson(['success' => false]);
    }

    public function test_recusa_valor_ausente(): void
    {
        [, , $sensor] = $this->cenarioComSensor();

        $response = $this->withHeader('Authorization', 'Bearer ' . $sensor->token)
            ->postJson("/api/sensores/{$sensor->id_sensor}/leituras", []);

        $response->assertStatus(422);
    }

    public function test_sensor_inexistente_retorna_404(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer qualquer')
            ->postJson('/api/sensores/999999/leituras', ['valor' => 6.5]);

        $response->assertStatus(404);
    }

    public function test_gera_alerta_quando_leitura_fora_do_limite(): void
    {
        Mail::fake();
        [, $lavoura, $sensor] = $this->cenarioComSensor();
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);

        $response = $this->withHeader('Authorization', 'Bearer ' . $sensor->token)
            ->postJson("/api/sensores/{$sensor->id_sensor}/leituras", ['valor' => 9.0]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('alertas', [
            'id_sensor' => $sensor->id_sensor,
        ]);
    }

    public function test_nao_gera_alerta_quando_leitura_dentro_do_limite(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor();
        $this->definirLimite($lavoura, 'ph', 5.5, 7.0);

        $this->withHeader('Authorization', 'Bearer ' . $sensor->token)
            ->postJson("/api/sensores/{$sensor->id_sensor}/leituras", ['valor' => 6.5]);

        $this->assertDatabaseCount('alertas', 0);
    }

    public function test_limita_requisicoes_por_token(): void
    {
        [, , $sensor] = $this->cenarioComSensor();
        $auth = 'Bearer ' . $sensor->token;

        for ($i = 0; $i < 60; $i++) {
            $this->withHeader('Authorization', $auth)
                ->postJson("/api/sensores/{$sensor->id_sensor}/leituras", ['valor' => 6.5])
                ->assertStatus(201);
        }

        $this->withHeader('Authorization', $auth)
            ->postJson("/api/sensores/{$sensor->id_sensor}/leituras", ['valor' => 6.5])
            ->assertStatus(429);
    }
}
