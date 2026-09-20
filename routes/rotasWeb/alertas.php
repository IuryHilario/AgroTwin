<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlertaController;

Route::prefix('alertas')->group(function () {
    Route::get('/', [AlertaController::class, 'index'])->name('alertas.index');
    Route::post('/{id}/marcar-lido', [AlertaController::class, 'marcarLido'])->name('alertas.marcarLido');
    Route::post('/marcar-todos-lidos', [AlertaController::class, 'marcarTodosLidos'])->name('alertas.marcarTodosLidos');
});
