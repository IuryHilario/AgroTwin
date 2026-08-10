<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropriedadeController;

// Rotas de propriedades
Route::prefix('propriedade')->group(function () {
    Route::get('/', [PropriedadeController::class, 'index'])->name('propriedade.index');
    Route::get('/inserir', [PropriedadeController::class, 'telaInserir'])->name('propriedade.inserir');
    Route::post('/', [PropriedadeController::class, 'store'])->name('propriedade.store');
    Route::get('/{id}', [PropriedadeController::class, 'show'])->name('propriedade.show');
    Route::get('/{id}/edit', [PropriedadeController::class, 'telaAlterar'])->name('propriedade.edit');
    Route::put('/{id}', [PropriedadeController::class, 'update'])->name('propriedade.update');
});

