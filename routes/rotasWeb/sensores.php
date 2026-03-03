<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorController;

Route::prefix('/sensores')->group(function () {
    Route::get('/', [SensorController::class, 'index'])->name('sensores.index');

});

