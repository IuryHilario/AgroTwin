<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IrrigacaoController;
use App\Http\Controllers\Api\SensorLeituraController;

// Endpoint de ingestão de leituras dos sensores (usado pelo ESP32/dispositivo no futuro).
// Autenticado por token do sensor (Bearer), não pela sessão web.
Route::post('/sensores/{sensor}/leituras', [SensorLeituraController::class, 'store'])
    ->middleware('throttle:sensor-ingestao')
    ->name('api.sensores.leituras.store');

// Configuração remota do sensor (ex.: intervalo de leitura), buscada pelo firmware no setup().
Route::get('/sensores/{sensor}/config', [SensorLeituraController::class, 'config'])
    ->middleware('throttle:sensor-ingestao')
    ->name('api.sensores.config');

// Consultado pelo dispositivo de irrigação para saber se deve manter a válvula solenoide aberta.
// Autenticado pelo token de irrigação da lavoura (Bearer), não pelo token de um sensor.
Route::get('/lavouras/{lavoura}/irrigacao', [IrrigacaoController::class, 'status'])
    ->middleware('throttle:sensor-ingestao')
    ->name('api.lavouras.irrigacao.status');
