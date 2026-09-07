<?php

use App\Http\Controllers\RecomendacaoController;
use Illuminate\Support\Facades\Route;

Route::prefix('recomendacoes')->group(function () {
    Route::get('/', [RecomendacaoController::class, 'index'])->name('recomendacoes.index');
    Route::get('/{id}', [RecomendacaoController::class, 'show'])->name('recomendacoes.show');
});
