<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerfilController;

Route::prefix('perfil')->name('perfil.')->group(function () {
    Route::get('/', [PerfilController::class, 'edit'])->name('edit');
    Route::put('/', [PerfilController::class, 'update'])->name('update');
    Route::put('/senha', [PerfilController::class, 'updateSenha'])->name('senha');
});
