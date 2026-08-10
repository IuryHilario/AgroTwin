<?php

namespace App\Http\Controllers\Propriedade;
use App\Models\Propriedade as PropriedadeModel;
use Illuminate\Support\Facades\Auth;


trait Tela
{
    public function telaInserir()
    {
        return view('propriedade.inserir');
    }

    public function telaAlterar($id)
    {
        $propriedade = PropriedadeModel::where('id_propriedade', $id)
                                  ->where('id_usuario', Auth::id())
                                  ->firstOrFail();

        return view('propriedade.edit', compact('propriedade'));
    }
}
