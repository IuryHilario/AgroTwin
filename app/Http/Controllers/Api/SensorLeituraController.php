<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sensor\StoreLeituraRequest;
use App\Models\Sensor;
use App\Services\AlertaService;
use App\Services\IrrigacaoService;
use App\Services\RecomendacaoService;
use App\Services\SensorReadingService;

class SensorLeituraController extends Controller
{
    /**
     * Recebe uma leitura de um sensor (chamado pelo ESP32/dispositivo no futuro,
     * direto ou via uma ponte MQTT->HTTP). Autenticado pelo token do próprio sensor.
     */
    public function store(
        StoreLeituraRequest $request,
        Sensor $sensor,
        SensorReadingService $service,
        AlertaService $alertaService,
        RecomendacaoService $recomendacaoService,
        IrrigacaoService $irrigacaoService
    ) {
        if (!$sensor->token || !hash_equals($sensor->token, (string) $request->bearerToken())) {
            return response()->json(['message' => 'Token inválido.'], 401);
        }

        $valor = (float) $request->validated()['valor'];
        $leitura = $service->registrar($sensor, $valor);
        $alertaService->verificar($leitura);
        $recomendacaoService->avaliarERegistrar($sensor, $valor);
        $irrigacaoService->verificar($leitura);

        return response()->json([
            'success' => true,
            'id_leitura' => $leitura->id_leitura,
            'dt_leitura' => $leitura->dt_leitura,
        ], 201);
    }

    /**
     * Configuração remota do dispositivo — hoje só o intervalo entre
     * leituras, configurável pelo agricultor por lavoura (tela de
     * Configurar Limites). O firmware busca isso uma vez no setup().
     */
    public function config(Sensor $sensor)
    {
        if (!$sensor->token || !hash_equals($sensor->token, (string) request()->bearerToken())) {
            return response()->json(['message' => 'Token inválido.'], 401);
        }

        $intervaloMinutos = $sensor->lavoura?->nu_intervalo_leitura_minutos ?? 5;

        return response()->json([
            'intervalo_leitura_ms' => $intervaloMinutos * 60 * 1000,
        ]);
    }
}
