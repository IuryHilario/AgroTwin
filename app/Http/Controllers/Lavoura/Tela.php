<?php

namespace App\Http\Controllers\Lavoura;

use App\Entity\PropriedadeEntity;
use App\Models\Propriedade;
use App\Services\SensorReadingService;

trait Tela
{
    public function telaInserir()
    {
        $propriedades = Propriedade::where('id_usuario', $this->idUsuario)->pluck('ds_nome', 'id_propriedade');
        return view('lavouras.inserir', compact('propriedades'));
    }
    public function telaAlterar($id)
    {
        $lavoura = $this->LavouraModel::findOrFail($id);
        $propriedades = PropriedadeEntity::pluckNomeByUsuario(Propriedade::query(), $this->idUsuario);
        return view('lavouras.edit', compact('lavoura', 'propriedades'));
    }

    public function telaMonitorar($id, SensorReadingService $leituraService)
    {
        $lavoura = $this->LavouraModel::with(['propriedade', 'sensores'])
                          ->whereHas('propriedade', function ($query) {
                              $query->where('id_usuario', $this->idUsuario);
                          })
                          ->where('id_lavoura', $id)
                          ->firstOrFail();

        $ultimasLeituras = $lavoura->sensores->mapWithKeys(fn ($sensor) => [
            $sensor->id_sensor => $leituraService->ultimaLeitura($sensor),
        ]);

        $recomendacoes = $lavoura->recomendacoes()->orderByDesc('dt_recomendacao')->limit(10)->get();
        $historicoIrrigacao = $lavoura->historicoIrrigacao()->orderByDesc('dt_inicio')->limit(10)->get();

        $dados = compact('lavoura', 'ultimasLeituras', 'recomendacoes', 'historicoIrrigacao');

        if (request()->ajax()) {
            $html = view('lavouras.monitor', $dados)->render();
            return response()->json([
                'success' => true,
                'data' => $lavoura,
                'html' => $html
            ]);
        }

        return view('lavouras.monitor', $dados);
    }
}
