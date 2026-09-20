<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Datas relativas em português ("há 5 dias") sem trocar o locale da
        // aplicação, que hoje só tem mensagens de validação em inglês.
        Carbon::setLocale('pt_BR');
        CarbonImmutable::setLocale('pt_BR');

        $this->loadMigrationsFrom([
            database_path('migrations/core'),
            database_path('migrations/custom'),
    ]);

        // Ingestão de leituras de sensores: limita por token do dispositivo (não por IP),
        // já que vários sensores podem estar atrás do mesmo roteador/IP. Protege contra
        // firmware com bug em loop e contra tentativa de força bruta do token.
        RateLimiter::for('sensor-ingestao', function (Request $request) {
            $chave = $request->bearerToken() ?? $request->ip();

            return Limit::perMinute(60)->by($chave);
        });
    }
}
