<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LavouraController;

// Rotas de lavouras
Route::prefix('lavouras')->group(function () {
    Route::get('/', [LavouraController::class, 'index'])->name('lavouras.index');
    Route::get('/inserir', [LavouraController::class, 'telaInserir'])->name('lavouras.inserir');
    Route::post('/', [LavouraController::class, 'store'])->name('lavouras.store');
    Route::get('/{id}/show', [LavouraController::class, 'show'])->name('lavouras.show');
    Route::get('/{id}/edit', [LavouraController::class, 'telaAlterar'])->name('lavouras.edit');
    Route::get('/{id}/monitorar', [LavouraController::class, 'telaMonitorar'])->name('lavouras.monitorar');
    Route::put('/{id}/update', [LavouraController::class, 'update'])->name('lavouras.update');
    Route::get('/{id}/limites', [LavouraController::class, 'telaConfigurarLimites'])->name('lavouras.limites');
    Route::post('/{id}/limites', [LavouraController::class, 'salvarLimites'])->name('lavouras.limites.salvar');
    Route::get('/{id}/irrigacao/iniciar', [LavouraController::class, 'iniciarIrrigacaoManual'])->name('lavouras.irrigacao.iniciar');
    Route::get('/{id}/irrigacao/parar', [LavouraController::class, 'pararIrrigacao'])->name('lavouras.irrigacao.parar');
});
