<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertaController extends Controller
{
    public function index(Request $request)
    {
        $alertas = Alerta::doUsuario(Auth::id());

        return view('alertas.index', compact('alertas'));
    }

    public function marcarLido($id)
    {
        $alerta = Alerta::whereHas('lavoura.propriedade', function ($query) {
            $query->where('id_usuario', Auth::id());
        })->findOrFail($id);

        $alerta->marcarComoLido();

        return redirect()->route('alertas.index')->with('success', 'Alerta marcado como lido.');
    }
}
