<?php

namespace App\Http\Controllers;

use App\Models\Recomendacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecomendacaoController extends Controller
{
    protected $model = Recomendacao::class;

    protected $resourceName = 'recomendacoes';

    public function index(Request $request)
    {
        $recomendacoes = Recomendacao::doUsuario(Auth::id());

        return view('recomendacoes.index', compact('recomendacoes'));
    }

    /**
     * Sobrescreve o show() genérico do CrudOperations: a relação com o
     * usuário passa por lavoura->propriedade (sem FK direta em
     * "recomendacoes"), e tanto "lavouras" quanto "propriedades" têm coluna
     * id_usuario — um whereHas("propriedade", ...) direto (via hasOneThrough)
     * gera "ambiguous column name" ao juntar as duas tabelas. O whereHas
     * aninhado evita isso.
     */
    public function show($id)
    {
        $recomendacao = Recomendacao::whereHas('lavoura.propriedade', function ($query) {
            $query->where('id_usuario', Auth::id());
        })->where('id_recomendacao', $id)->firstOrFail();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('recomendacoes.detalhar', ['recomendacoe' => $recomendacao])->render(),
            ]);
        }

        return view('recomendacoes.detalhar', ['recomendacoe' => $recomendacao]);
    }
}
