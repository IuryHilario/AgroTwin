<?php

use App\Http\Controllers\LocalidadeController;
use Illuminate\Support\Facades\Route;

// Busca de município e prévia do clima (Open-Meteo), usadas pelo seletor de
// localidade dos formulários. O limite protege a cota da API contra loop de
// digitação ou abuso.
Route::prefix('localidades')->middleware('throttle:40,1')->group(function () {
    Route::get('/buscar', [LocalidadeController::class, 'buscar'])->name('localidades.buscar');
    Route::get('/clima', [LocalidadeController::class, 'clima'])->name('localidades.clima');
});
