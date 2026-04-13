<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InsumoController;

// Rotas de insumos
    Route::get('/insumos', [InsumoController::class, 'index'])->name('insumos.index');
    Route::get('/insumos/inserir', [InsumoController::class, 'telaInserir'])->name('insumos.inserir');
    Route::post('/insumos', [InsumoController::class, 'store'])->name('insumos.store');
    Route::get('/insumos/{id}/estoque', [InsumoController::class, 'telaEstoque'])->name('insumos.estoque');
    Route::get('/insumos/{id}/estoque/nova-movimentacao', [InsumoController::class, 'telaEstoqueNovo'])->name('insumos.estoque.create');
    Route::post('/insumos/{id}/estoque/movimentacao', [InsumoController::class, 'storeEstoque'])->name('insumos.estoque.store');
    Route::get('/insumos/aplicacao/{id}', [InsumoController::class, 'telaAplicacao'])->name('insumos.aplicacao');
    Route::get('/insumos/aplicacao/{id}/criar', [InsumoController::class, 'telaAplicacaoNova'])->name('insumos.aplicacao.create');
    Route::post('/insumos/aplicacao/{id}', [InsumoController::class, 'storeAplicacao'])->name('insumos.aplicacao.store');
    Route::get('/insumos/relatorio/{id}', [InsumoController::class, 'telaRelatorio'])->name('insumos.relatorio');
    Route::get('/insumos/{id}', [InsumoController::class, 'show'])->name('insumos.show');
    Route::get('/insumos/{id}/edit', [InsumoController::class, 'telaEditar'])->name('insumos.edit');
    Route::put('/insumos/{id}', [InsumoController::class, 'update'])->name('insumos.update');
