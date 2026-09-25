<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Busca de municípios pela API de geocodificação do Open-Meteo: transforma
 * "Goiânia" em latitude/longitude, que é o que a previsão do tempo precisa.
 *
 * Sem chave de API. Distingue "não achou nada" (lista vazia) de "a API não
 * respondeu" (null): a tela mostra mensagens diferentes, e a falha não fica
 * no cache se passando por resultado vazio.
 */
class LocalidadeService
{
    private const URL = 'https://geocoding-api.open-meteo.com/v1/search';

    private const CACHE_SUCESSO_HORAS = 24;

    /**
     * Falha também é lembrada, mas por pouco tempo: evita que cada abertura do
     * dashboard espere o timeout de novo, sem esconder a busca por muito tempo
     * quando a rede voltar.
     */
    private const CACHE_FALHA_MIN = 2;

    private const FALHOU = '__falhou__';

    /**
     * @return array<int, array{nome: string, rotulo: string, latitude: float, longitude: float, estado: ?string, pais: ?string}>|null
     *         null quando a API não respondeu.
     */
    public function buscar(string $termo, int $limite = 6): ?array
    {
        $termo = trim($termo);

        if (mb_strlen($termo) < 2) {
            return [];
        }

        $chave = 'localidade:' . mb_strtolower($termo) . ':' . $limite;
        $guardado = Cache::get($chave);

        if ($guardado !== null) {
            return $guardado === self::FALHOU ? null : $guardado;
        }

        $resultados = $this->consultar($termo, $limite);

        Cache::put(
            $chave,
            $resultados ?? self::FALHOU,
            $resultados === null ? now()->addMinutes(self::CACHE_FALHA_MIN) : now()->addHours(self::CACHE_SUCESSO_HORAS)
        );

        return $resultados;
    }

    /**
     * Primeiro resultado da busca — usado para dar coordenadas a propriedades
     * antigas, que só têm o nome da cidade em texto livre.
     */
    public function primeira(string $termo): ?array
    {
        return $this->buscar($termo, 1)[0] ?? null;
    }

    private function consultar(string $termo, int $limite): ?array
    {
        try {
            $resposta = Http::connectTimeout(3)->timeout(5)->get(self::URL, [
                'name' => $termo,
                'count' => $limite,
                'language' => 'pt',
                'format' => 'json',
            ]);
        } catch (\Throwable) {
            return null;
        }

        if (!$resposta->successful()) {
            return null;
        }

        // Sem resultado, a API simplesmente omite a chave "results".
        return collect($resposta->json('results', []))
            ->map(fn (array $local) => [
                'nome' => $local['name'],
                'rotulo' => $this->rotulo($local),
                'latitude' => round((float) $local['latitude'], 6),
                'longitude' => round((float) $local['longitude'], 6),
                'estado' => $local['admin1'] ?? null,
                'pais' => $local['country'] ?? null,
            ])
            ->values()
            ->all();
    }

    /** "Goiânia, Goiás, Brasil" — sem repetir o nome quando o município é homônimo do estado. */
    private function rotulo(array $local): string
    {
        return collect([$local['name'], $local['admin1'] ?? null, $local['country'] ?? null])
            ->filter()
            ->unique()
            ->implode(', ');
    }
}
