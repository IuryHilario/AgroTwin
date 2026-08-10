<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlertaController;

Route::prefix('alertas')->group(function () {
    Route::get('/', [AlertaController::class, 'index'])->name('alertas.index');
    Route::get('/{id}/marcar-lido', [AlertaController::class, 'marcarLido'])->name('alertas.marcarLido');
});
