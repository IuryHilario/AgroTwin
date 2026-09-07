<?php

namespace App\Traits;

use App\Models\Propriedade;
use App\Models\Sensor;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Resolve a propriedade/lavoura selecionada nas telas que filtram dados por
 * propriedade + lavoura (Dashboard, Relatórios). Centraliza esse fluxo para
 * não repetir a mesma resolução de request em cada controller.
 */
trait SelecionaPropriedadeLavoura
{
    /**
     * @return array{0: \Illuminate\Support\Collection, 1: Propriedade, 2: \Illuminate\Support\Collection, 3: ?\App\Models\Lavoura}
     */
    protected function selecionarPropriedadeELavoura(Request $request, User $usuario): array
    {
        $propriedades = Propriedade::byUsuario($usuario->id_usuario)->ativas()->get();

        if ($propriedades->isEmpty()) {
            return [$propriedades, null, collect(), null];
        }

        $selectedPropriedadeId = $request->input('id_propriedade');
        $selectedPropriedade = $selectedPropriedadeId
            ? $propriedades->firstWhere('id_propriedade', (int) $selectedPropriedadeId)
            : null;

        if (! $selectedPropriedade) {
            $padrao = $usuario->id_propriedade_padrao
                ? $propriedades->firstWhere('id_propriedade', $usuario->id_propriedade_padrao)
                : null;

            $selectedPropriedade = $padrao ?? $propriedades->first();
        }

        $lavouras = $selectedPropriedade->lavouras;

        $selectedLavouraId = $request->input('id_lavoura');
        $selectedLavoura = $selectedLavouraId
            ? $lavouras->firstWhere('id_lavoura', (int) $selectedLavouraId)
            : $lavouras->first();

        return [$propriedades, $selectedPropriedade, $lavouras, $selectedLavoura];
    }

    /**
     * Sensores da lavoura selecionada, ou os sensores soltos da propriedade
     * (sem lavoura atribuída) quando não há lavoura selecionada.
     */
    protected function sensoresDaSelecao(Propriedade $propriedade, $lavoura)
    {
        return $lavoura
            ? $lavoura->sensores
            : Sensor::where('id_propriedade', $propriedade->id_propriedade)->whereNull('id_lavoura')->get();
    }
}
