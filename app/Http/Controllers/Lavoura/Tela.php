<?php

namespace App\Http\Controllers\Lavoura;

use App\Entity\PropriedadeEntity;
use App\Models\Propriedade;
use App\Services\SensorReadingService;

trait Tela
{
    public function telaInserir()
    {
        return view('lavouras.inserir', [
            'propriedades' => PropriedadeEntity::pluckNomeByUsuario(Propriedade::query(), $this->idUsuario),
        ]);
    }

    public function telaAlterar($id)
    {
        return view('lavouras.edit', [
            'lavoura' => $this->buscarDoUsuario($this->model, $id),
            'propriedades' => PropriedadeEntity::pluckNomeByUsuario(Propriedade::query(), $this->idUsuario),
        ]);
    }

    public function telaMonitorar($id, SensorReadingService $leituraService)
    {
        $lavoura = $this->consultaDoUsuario($this->model)
            ->with(['propriedade', 'sensores'])
            ->where('id_lavoura', $id)
            ->firstOrFail();

        $dados = [
            'lavoura' => $lavoura,
            'ultimasLeituras' => $lavoura->sensores->mapWithKeys(fn ($sensor) => [
                $sensor->id_sensor => $leituraService->ultimaLeitura($sensor),
            ]),
            'recomendacoes' => $lavoura->recomendacoes()->orderByDesc('dt_recomendacao')->limit(10)->get(),
            'historicoIrrigacao' => $lavoura->historicoIrrigacao()->orderByDesc('dt_inicio')->limit(10)->get(),
        ];

        return $this->responderView('lavouras.monitor', $dados, $lavoura);
    }
}
