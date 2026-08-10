<?php

namespace App\Http\Controllers\Lavoura;

use App\Services\IrrigacaoService;

trait Irrigacao
{
    public function iniciarIrrigacaoManual($id, IrrigacaoService $irrigacaoService)
    {
        $lavoura = $this->LavouraModel::whereHas('propriedade', function ($query) {
            $query->where('id_usuario', $this->idUsuario);
        })->where('id_lavoura', $id)->firstOrFail();

        $irrigacaoService->iniciar($lavoura, 'manual', 'Irrigação iniciada manualmente pelo agricultor.');

        return redirect()->back()->with('success', 'Irrigação iniciada.');
    }

    public function pararIrrigacao($id, IrrigacaoService $irrigacaoService)
    {
        $lavoura = $this->LavouraModel::whereHas('propriedade', function ($query) {
            $query->where('id_usuario', $this->idUsuario);
        })->where('id_lavoura', $id)->firstOrFail();

        $irrigacaoService->encerrar($lavoura);

        return redirect()->back()->with('success', 'Irrigação encerrada.');
    }
}
