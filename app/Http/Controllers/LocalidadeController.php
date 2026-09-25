<?php

namespace App\Http\Controllers;

use App\Services\ClimaService;
use App\Services\LocalidadeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Endpoints consumidos pelo seletor de localidade (<x-form.localidade>):
 * busca de município e prévia do clima no ponto escolhido. Passam pelo
 * servidor, e não direto do navegador ao Open-Meteo, para ficar tudo num
 * ponto só — com cache, limite de requisições e testável.
 */
class LocalidadeController extends Controller
{
    public function buscar(Request $request, LocalidadeService $localidades): JsonResponse
    {
        $dados = $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        $resultados = $localidades->buscar($dados['q']);

        // 200 nos dois casos: "indisponivel" diz à tela para sugerir o mapa em
        // vez de afirmar que o município não existe.
        return response()->json([
            'resultados' => $resultados ?? [],
            'indisponivel' => $resultados === null,
        ]);
    }

    public function clima(Request $request, ClimaService $clima): JsonResponse
    {
        $dados = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        return response()->json([
            'clima' => $clima->previsao((float) $dados['latitude'], (float) $dados['longitude']),
        ]);
    }
}
