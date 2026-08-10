<?php

namespace App\Http\Controllers\Lavoura;

use App\Enums\TipoSensor;
use App\Models\ConfiguracaoLimite;
use Illuminate\Http\Request;

trait Limites
{
    public function telaConfigurarLimites($id)
    {
        $lavoura = $this->LavouraModel::whereHas('propriedade', function ($query) {
            $query->where('id_usuario', $this->idUsuario);
        })->where('id_lavoura', $id)->firstOrFail();

        $limites = ConfiguracaoLimite::porLavoura($id);
        $tiposSensor = TipoSensor::cases();

        if (request()->ajax()) {
            $html = view('lavouras.configurar-limites', compact('lavoura', 'limites', 'tiposSensor'))->render();
            return response()->json(['success' => true, 'html' => $html]);
        }

        return view('lavouras.configurar-limites', compact('lavoura', 'limites', 'tiposSensor'));
    }

    public function salvarLimites(Request $request, $id)
    {
        $lavoura = $this->LavouraModel::whereHas('propriedade', function ($query) {
            $query->where('id_usuario', $this->idUsuario);
        })->where('id_lavoura', $id)->firstOrFail();

        $dados = $request->validate([
            'limites' => 'array',
            'limites.*.valor_min' => 'nullable|numeric',
            'limites.*.valor_max' => 'nullable|numeric',
        ]);

        ConfiguracaoLimite::salvarParaLavoura($lavoura->id_lavoura, $dados['limites'] ?? []);

        return redirect()->route('lavouras.index')
                        ->with('success', 'Limites da lavoura atualizados com sucesso!');
    }
}
