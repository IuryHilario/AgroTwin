<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorController;

Route::prefix('/sensores')->group(function () {
    Route::get('/', [SensorController::class, 'index'])->name('sensores.index');
    Route::get('/inserir', [SensorController::class, 'telaInserir'])->name('sensores.inserir');
    Route::post('/', [SensorController::class, 'store'])->name('sensores.store');
    Route::get('/{id}/show', [SensorController::class, 'show'])->name('sensores.show');
    Route::get('/{id}/edit', [SensorController::class, 'telaAlterar'])->name('sensores.edit');
    Route::put('/{id}/update', [SensorController::class, 'update'])->name('sensores.update');
});
