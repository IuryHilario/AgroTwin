<?php

namespace App\Http\Controllers\Sensor;

use App\Models\Propriedade;
use App\Models\Lavoura;
use Illuminate\Support\Facades\Auth;

trait Tela
{
    public function telaInserir()
    {
        $propriedades = Propriedade::where('id_usuario', Auth::id())->pluck('ds_nome', 'id_propriedade');
        $lavouras = Lavoura::whereHas('propriedade', function ($query) {
            $query->where('id_usuario', Auth::id());
        })->get(['id_lavoura', 'ds_cultura', 'id_propriedade']);

        return view('sensores.inserir', compact('propriedades', 'lavouras'));
    }

    public function telaAlterar($id)
    {
        $sensor = $this->SensorModel::findOrFail($id);
        $propriedades = Propriedade::where('id_usuario', $this->idUsuario)->pluck('ds_nome', 'id_propriedade');
        $lavouras = Lavoura::whereHas('propriedade', function ($query) {
            $query->where('id_usuario', $this->idUsuario);
        })->get(['id_lavoura', 'ds_cultura', 'id_propriedade']);

        return view('sensores.edit', compact('sensor', 'propriedades', 'lavouras'));
    }
}
