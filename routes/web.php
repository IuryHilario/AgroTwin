<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Rotas de autenticação GET
Route::get('/login', [AuthController::class, 'loginView'])->name('login');
Route::get('/register', [AuthController::class, 'registerView'])->name('register');
Route::get('/esqueci-senha', [AuthController::class, 'forgotPasswordView'])->name('password.request');
Route::get('/redefinir-senha/{token}', [AuthController::class, 'resetPasswordView'])->name('password.reset');

// Rotas de autenticação POST — throttle:5,1 = no máximo 5 tentativas por minuto por IP+email,
// pra dificultar força bruta na senha.
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/esqueci-senha', [AuthController::class, 'sendResetLink'])->middleware('throttle:5,1')->name('password.email');
Route::post('/redefinir-senha', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1')->name('password.update');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Incluir rotas de propriedade
    require __DIR__ . '/rotasWeb/propriedade.php';

    // Rotas de lavouras
    require __DIR__ . '/rotasWeb/lavouras.php';

    // Rotas de insumos
    require __DIR__ . '/rotasWeb/insumos.php';

    // Rotas dos Sensores
    require __DIR__ . '/rotasWeb/sensores.php';

    // // Rotas das Recomendação IA
    require __DIR__ . '/rotasWeb/recomendacao.php';

    // // Rotas de Alertas
    require __DIR__ . '/rotasWeb/alertas.php';

    // // Rotas de Relatórios
    require __DIR__ . '/rotasWeb/relatorios.php';

    // // Rotas de Perfil
    require __DIR__ . '/rotasWeb/perfil.php';

    // // Rotas de Configurações
    require __DIR__ . '/rotasWeb/configuracoes.php';

    // Busca de município e clima (Open-Meteo) para o seletor de localidade
    require __DIR__ . '/rotasWeb/localidades.php';
});
