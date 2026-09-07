<?php

namespace App\Http\Controllers\Sensor;

use App\Entity\PropriedadeEntity;
use App\Models\Lavoura;
use App\Models\Propriedade;
use Illuminate\Support\Facades\Auth;

trait Tela
{
    public function telaInserir()
    {
        $propriedades = PropriedadeEntity::pluckNomeByUsuario(Propriedade::query(), Auth::id());
        $lavouras = Lavoura::whereHas('propriedade', function ($query) {
            $query->where('id_usuario', Auth::id())->where('fl_inativo', false);
        })->get(['id_lavoura', 'ds_cultura', 'id_propriedade']);

        return view('sensores.inserir', compact('propriedades', 'lavouras'));
    }

    public function telaAlterar($id)
    {
        $sensor = $this->SensorModel::findOrFail($id);
        $propriedades = PropriedadeEntity::pluckNomeByUsuario(Propriedade::query(), $this->idUsuario);
        $lavouras = Lavoura::whereHas('propriedade', function ($query) {
            $query->where('id_usuario', $this->idUsuario)->where('fl_inativo', false);
        })->get(['id_lavoura', 'ds_cultura', 'id_propriedade']);

        return view('sensores.edit', compact('sensor', 'propriedades', 'lavouras'));
    }
}
