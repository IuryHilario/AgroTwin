<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Clima atual da cidade da propriedade (OpenWeather). Usado no painel do
 * dashboard e nos detalhes da propriedade.
 *
 * Nunca derruba a tela: sem chave, cidade desconhecida ou API fora do ar,
 * devolve null e a tela simplesmente não mostra o bloco de clima. O
 * resultado fica em cache para não chamar a API a cada carregamento.
 */
class ClimaService
{
    private const CACHE_SUCESSO_MIN = 30;
    private const CACHE_FALHA_MIN = 10;

    public function atual(?string $cidade): ?array
    {
        $cidade = trim((string) $cidade);
        $chave = config('services.openweather.key');

        if ($cidade === '' || empty($chave)) {
            return null;
        }

        $cacheKey = 'clima:' . mb_strtolower($cidade);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey) ?: null;
        }

        $clima = $this->consultar($cidade, $chave);

        // Falha também vai para o cache (como false), para não martelar a API.
        Cache::put($cacheKey, $clima ?? false, now()->addMinutes($clima ? self::CACHE_SUCESSO_MIN : self::CACHE_FALHA_MIN));

        return $clima;
    }

    private function consultar(string $cidade, string $chave): ?array
    {
        try {
            $resposta = Http::timeout(3)->get('https://api.openweathermap.org/data/2.5/weather', [
                'q' => $cidade,
                'appid' => $chave,
                'units' => 'metric',
                'lang' => 'pt_br',
            ]);
        } catch (\Throwable) {
            return null;
        }

        if (!$resposta->successful()) {
            return null;
        }

        $dados = $resposta->json();

        return [
            'cidade' => $dados['name'] ?? $cidade,
            'temperatura' => round($dados['main']['temp']),
            'sensacao' => round($dados['main']['feels_like']),
            'umidade' => $dados['main']['humidity'],
            'descricao' => ucfirst($dados['weather'][0]['description'] ?? ''),
            'icone' => $this->icone($dados['weather'][0]['main'] ?? ''),
            // A API devolve m/s em unidades métricas.
            'vento_kmh' => round(($dados['wind']['speed'] ?? 0) * 3.6),
        ];
    }

    private function icone(string $condicao): string
    {
        return match ($condicao) {
            'Clear' => 'fa-sun',
            'Clouds' => 'fa-cloud',
            'Rain', 'Drizzle' => 'fa-cloud-rain',
            'Thunderstorm' => 'fa-cloud-bolt',
            'Snow' => 'fa-snowflake',
            default => 'fa-smog',
        };
    }
}
