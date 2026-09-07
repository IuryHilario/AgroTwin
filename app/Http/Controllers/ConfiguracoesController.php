<?php

namespace App\Http\Controllers;

use App\Entity\PropriedadeEntity;
use App\Http\Requests\Configuracoes\UpdateConfiguracoesRequest;
use App\Models\Propriedade;
use Illuminate\Support\Facades\Auth;

class ConfiguracoesController extends Controller
{
    public function edit()
    {
        $propriedades = PropriedadeEntity::pluckNomeByUsuario(Propriedade::query(), Auth::id());

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
