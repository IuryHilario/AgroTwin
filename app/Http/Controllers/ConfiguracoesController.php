<?php

namespace App\Http\Controllers;

use App\Http\Requests\Configuracoes\UpdateConfiguracoesRequest;
use App\Models\Propriedade;
use Illuminate\Support\Facades\Auth;

class ConfiguracoesController extends Controller
{
    public function edit()
    {
        $propriedades = Propriedade::where('id_usuario', Auth::id())->pluck('ds_nome', 'id_propriedade');

        return view('configuracoes.edit', [
            'usuario' => Auth::user(),
            'propriedades' => $propriedades,
        ]);
    }

    public function update(UpdateConfiguracoesRequest $request)
    {
        Auth::user()->update([
            'fl_notificar_email_alerta' => $request->boolean('fl_notificar_email_alerta'),
            'id_propriedade_padrao' => $request->id_propriedade_padrao,
        ]);

        return redirect()->route('configuracoes.edit')->with('success', 'Configurações atualizadas com sucesso!');
    }
}
