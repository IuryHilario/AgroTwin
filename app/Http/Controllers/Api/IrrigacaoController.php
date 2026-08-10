<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lavoura;

class IrrigacaoController extends Controller
{
    /**
     * Consultado pelo dispositivo de irrigação (ESP32 + relé/válvula
     * solenoide) para saber se deve manter a válvula aberta. Autenticado
     * pelo token de irrigação da própria lavoura.
     */
    public function status(Lavoura $lavoura)
    {
        if (!$lavoura->token_irrigacao || !hash_equals($lavoura->token_irrigacao, (string) request()->bearerToken())) {
            return response()->json(['message' => 'Token inválido.'], 401);
        }

        return response()->json([
            'irrigar' => (bool) $lavoura->fl_irrigacao_ativa,
        ]);
    }
}
