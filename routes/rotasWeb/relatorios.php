<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RelatorioController;

Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
Route::get('/relatorios/pdf', [RelatorioController::class, 'pdf'])->name('relatorios.pdf');
