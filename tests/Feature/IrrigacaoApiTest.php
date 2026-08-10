<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioSensor;
use Tests\TestCase;

class IrrigacaoApiTest extends TestCase
{
    use RefreshDatabase;
    use CriaCenarioSensor;

    public function test_dispositivo_consulta_status_da_irrigacao_com_token_correto(): void
    {
        [, $lavoura] = $this->cenarioComSensor();

        $response = $this->withHeader('Authorization', 'Bearer ' . $lavoura->token_irrigacao)
            ->getJson("/api/lavouras/{$lavoura->id_lavoura}/irrigacao");

        $response->assertOk()->assertJson(['irrigar' => false]);
    }

    public function test_reflete_irrigacao_ativa_no_status(): void
    {
        [, $lavoura] = $this->cenarioComSensor();
        $lavoura->update(['fl_irrigacao_ativa' => true]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $lavoura->token_irrigacao)
            ->getJson("/api/lavouras/{$lavoura->id_lavoura}/irrigacao");

        $response->assertOk()->assertJson(['irrigar' => true]);
    }

    public function test_recusa_status_com_token_errado(): void
    {
        [, $lavoura] = $this->cenarioComSensor();

        $response = $this->withHeader('Authorization', 'Bearer token-errado')
            ->getJson("/api/lavouras/{$lavoura->id_lavoura}/irrigacao");

        $response->assertStatus(401);
    }

    public function test_dispositivo_consulta_intervalo_de_leitura_configurado(): void
    {
        [, $lavoura, $sensor] = $this->cenarioComSensor();
        $lavoura->update(['nu_intervalo_leitura_minutos' => 10]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $sensor->token)
            ->getJson("/api/sensores/{$sensor->id_sensor}/config");

        $response->assertOk()->assertJson(['intervalo_leitura_ms' => 10 * 60 * 1000]);
    }
}
