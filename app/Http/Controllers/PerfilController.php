<?php

namespace App\Http\Controllers;

use App\Http\Requests\Perfil\UpdatePerfilRequest;
use App\Http\Requests\Perfil\UpdateSenhaRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function edit()
    {
        return view('perfil.edit', ['usuario' => Auth::user()]);
    }

    public function update(UpdatePerfilRequest $request)
    {
        Auth::user()->update($request->validated());

        return redirect()->route('perfil.edit')->with('success', 'Perfil atualizado com sucesso!');
    }

    public function updateSenha(UpdateSenhaRequest $request)
    {
        Auth::user()->update(['password' => Hash::make($request->nova_senha)]);

        return redirect()->route('perfil.edit')->with('success', 'Senha alterada com sucesso!');
    }
}
