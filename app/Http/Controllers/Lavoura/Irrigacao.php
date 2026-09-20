<?php

namespace App\Http\Controllers\Lavoura;

use App\Services\IrrigacaoService;

trait Irrigacao
{
    public function iniciarIrrigacaoManual($id, IrrigacaoService $irrigacaoService)
    {
        $lavoura = $this->buscarDoUsuario($this->model, $id);

        $irrigacaoService->iniciar($lavoura, 'manual', 'Irrigação iniciada manualmente pelo agricultor.');

        return $this->respostaIrrigacao('Irrigação iniciada.');
    }

    public function pararIrrigacao($id, IrrigacaoService $irrigacaoService)
    {
        $lavoura = $this->buscarDoUsuario($this->model, $id);

        $irrigacaoService->encerrar($lavoura);

        return $this->respostaIrrigacao('Irrigação encerrada.');
    }

    private function respostaIrrigacao(string $mensagem)
    {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => $mensagem]);
        }

        return redirect()->back()->with('success', $mensagem);
    }
}
