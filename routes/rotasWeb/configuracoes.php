<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConfiguracoesController;

Route::prefix('configuracoes')->name('configuracoes.')->group(function () {
    Route::get('/', [ConfiguracoesController::class, 'edit'])->name('edit');
    Route::put('/', [ConfiguracoesController::class, 'update'])->name('update');
});
