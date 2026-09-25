<?php

namespace App\Services;

use App\Models\Propriedade;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Condições do tempo pela API do Open-Meteo, a partir das coordenadas da
 * propriedade: tempo atual, chance de chuva das próximas horas e previsão
 * dos próximos dias, incluindo a evapotranspiração de referência (ET0).
 *
 * Substitui a consulta ao OpenWeather por nome de cidade. Não precisa de
 * chave de API. Nunca derruba a tela: sem coordenadas ou com a API fora do
 * ar, devolve null e a tela simplesmente não mostra o bloco de clima.
 */
class ClimaService
{
    private const URL = 'https://api.open-meteo.com/v1/forecast';

    private const CACHE_SUCESSO_MIN = 30;
    private const CACHE_FALHA_MIN = 10;

    /** Dias de previsão devolvidos. */
    private const DIAS = 5;

    /** Janela usada para "chance de chuva nas próximas horas". */
    private const HORAS_PROXIMAS = 6;

    public function __construct(private LocalidadeService $localidades)
    {
    }

    /**
     * Clima da propriedade. Propriedades cadastradas antes das coordenadas
     * só têm o nome da cidade: nesse caso a cidade é geocodificada (com
     * cache), para o bloco de clima não sumir até a propriedade ser editada.
     */
    public function daPropriedade(Propriedade $propriedade): ?array
    {
        if ($propriedade->nu_latitude !== null && $propriedade->nu_longitude !== null) {
            return $this->previsao($propriedade->nu_latitude, $propriedade->nu_longitude);
        }

        $local = $propriedade->ds_localizacao
            ? $this->localidades->primeira($propriedade->ds_localizacao)
            : null;

        return $local ? $this->previsao($local['latitude'], $local['longitude']) : null;
    }

    public function previsao(float $latitude, float $longitude): ?array
    {
        // Arredondar a chave evita um cache por centímetro: 2 casas ≈ 1 km,
        // bem abaixo da resolução dos modelos de previsão.
        $cacheKey = sprintf('clima:%.2f:%.2f', $latitude, $longitude);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey) ?: null;
        }

        $clima = $this->consultar($latitude, $longitude);

        // Falha também vai para o cache (como false), para não martelar a API.
        Cache::put($cacheKey, $clima ?? false, now()->addMinutes($clima ? self::CACHE_SUCESSO_MIN : self::CACHE_FALHA_MIN));

        return $clima;
    }

    private function consultar(float $latitude, float $longitude): ?array
    {
        try {
            $resposta = Http::connectTimeout(3)->timeout(5)->get(self::URL, [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,precipitation,weather_code,wind_speed_10m',
                'hourly' => 'precipitation_probability',
                'daily' => 'weather_code,temperature_2m_max,temperature_2m_min,precipitation_sum,precipitation_probability_max,et0_fao_evapotranspiration',
                'timezone' => 'auto',
                'forecast_days' => self::DIAS,
                'forecast_hours' => self::HORAS_PROXIMAS,
            ]);
        } catch (\Throwable) {
            return null;
        }

        if (!$resposta->successful() || !$resposta->json('current')) {
            return null;
        }

        $dados = $resposta->json();
        $atual = $dados['current'];
        $codigo = (int) $atual['weather_code'];

        return [
            'temperatura' => (int) round($atual['temperature_2m']),
            'sensacao' => (int) round($atual['apparent_temperature']),
            'umidade' => (int) $atual['relative_humidity_2m'],
            // A API já devolve o vento em km/h por padrão.
            'vento_kmh' => (int) round($atual['wind_speed_10m']),
            'precipitacao_mm' => (float) $atual['precipitation'],
            'descricao' => $this->descricao($codigo),
            'icone' => $this->icone($codigo),
            'chuva_proximas_horas' => $this->maximo($dados['hourly']['precipitation_probability'] ?? []),
            'dias' => $this->dias($dados['daily'] ?? []),
            'fuso' => $dados['timezone'] ?? null,
        ];
    }

    /**
     * Previsão diária já pronta para exibir: um item por dia com a chance
     * máxima de chuva, o volume previsto e a ET0 (mm que a cultura de
     * referência perde para a atmosfera — base do cálculo de irrigação).
     */
    private function dias(array $diario): array
    {
        return collect($diario['time'] ?? [])
            ->map(fn (string $data, int $i) => [
                'data' => $data,
                'rotulo' => $this->rotuloDoDia($data, $i),
                'icone' => $this->icone((int) ($diario['weather_code'][$i] ?? 0)),
                'descricao' => $this->descricao((int) ($diario['weather_code'][$i] ?? 0)),
                'maxima' => isset($diario['temperature_2m_max'][$i]) ? (int) round($diario['temperature_2m_max'][$i]) : null,
                'minima' => isset($diario['temperature_2m_min'][$i]) ? (int) round($diario['temperature_2m_min'][$i]) : null,
                'chance_chuva' => isset($diario['precipitation_probability_max'][$i]) ? (int) $diario['precipitation_probability_max'][$i] : null,
                'chuva_mm' => isset($diario['precipitation_sum'][$i]) ? (float) $diario['precipitation_sum'][$i] : null,
                'et0_mm' => isset($diario['et0_fao_evapotranspiration'][$i]) ? (float) $diario['et0_fao_evapotranspiration'][$i] : null,
            ])
            ->values()
            ->all();
    }

    private function rotuloDoDia(string $data, int $indice): string
    {
        return match ($indice) {
            0 => 'Hoje',
            1 => 'Amanhã',
            default => ucfirst(Carbon::parse($data)->locale('pt_BR')->isoFormat('ddd')),
        };
    }

    private function maximo(array $valores): ?int
    {
        $valores = array_filter($valores, fn ($valor) => $valor !== null);

        return $valores ? (int) max($valores) : null;
    }

    /** Códigos WMO usados pelo Open-Meteo. */
    private function descricao(int $codigo): string
    {
        return match (true) {
            $codigo === 0 => 'Céu limpo',
            $codigo === 1 => 'Predominantemente limpo',
            $codigo === 2 => 'Parcialmente nublado',
            $codigo === 3 => 'Nublado',
            in_array($codigo, [45, 48], true) => 'Neblina',
            $codigo >= 51 && $codigo <= 57 => 'Garoa',
            $codigo >= 61 && $codigo <= 67 => 'Chuva',
            $codigo >= 71 && $codigo <= 77 => 'Neve',
            $codigo >= 80 && $codigo <= 82 => 'Pancadas de chuva',
            in_array($codigo, [85, 86], true) => 'Pancadas de neve',
            $codigo >= 95 => 'Tempestade',
            default => 'Indefinido',
        };
    }

    private function icone(int $codigo): string
    {
        return match (true) {
            $codigo <= 1 => 'fa-sun',
            $codigo === 2 => 'fa-cloud-sun',
            $codigo === 3 => 'fa-cloud',
            in_array($codigo, [45, 48], true) => 'fa-smog',
            ($codigo >= 51 && $codigo <= 67) || ($codigo >= 80 && $codigo <= 82) => 'fa-cloud-rain',
            ($codigo >= 71 && $codigo <= 77) || in_array($codigo, [85, 86], true) => 'fa-snowflake',
            $codigo >= 95 => 'fa-cloud-bolt',
            default => 'fa-cloud',
        };
    }
}
