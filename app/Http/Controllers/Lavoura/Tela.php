<?php

namespace App\Http\Controllers\Lavoura;

use App\Entity\PropriedadeEntity;
use App\Models\Propriedade;

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

    public function telaMonitorar($id)
    {
        $lavoura = $this->LavouraModel::with(['propriedade'])
                          ->whereHas('propriedade', function ($query) {
                              $query->where('id_usuario', $this->idUsuario);
                          })
                          ->where('id_lavoura', $id)
                          ->firstOrFail();

        if (request()->ajax()) {
            $html = view('lavouras.monitor', compact('lavoura'))->render();
            return response()->json([
                'success' => true,
                'data' => $lavoura,
                'html' => $html
            ]);
        }

        return view('lavouras.monitor', compact('lavoura'));
    }
}
