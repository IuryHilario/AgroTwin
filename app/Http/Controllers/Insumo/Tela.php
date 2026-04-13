<?php

namespace App\Http\Controllers\Insumo;

use App\Enums\TipoInsumo;
use App\Enums\TipoUnidadeMedida;
use App\Models\Lavoura;
use Illuminate\Support\Facades\Auth;


trait Tela
{
    public function telaInserir()
    {
        $tiposInsumo = TipoInsumo::cases();
        $unidadesMedida = TipoUnidadeMedida::cases();

        return view('insumos.inserir', compact('tiposInsumo', 'unidadesMedida'));
    }

    public function telaEditar($id)
    {
        $insumo = $this->insumoModel::findOrFail($id);

        if ($insumo->id_usuario !== Auth::id()) {
            return redirect()->route('insumos.index')->with('error', 'Acesso negado.');
        }

        return view('insumos.edit', compact('insumo'));
    }

    public function telaEstoque($id)
    {
        $insumo = $this->insumoModel::where('id_insumo', $id)
                         ->where('id_usuario', Auth::id())
                         ->with(['movimentacoes' => function($query) {
                             $query->orderBy('dt_movimentacao', 'desc')
                                   ->orderBy('created_at', 'desc')
                                   ->limit(10);
                         }])
                         ->firstOrFail();

        if (request()->expectsJson()) {
            $html = view('insumos.estoque', compact('insumo'))->render();
            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        }

        return $this->handleCustomFunction('estoque', $id);
    }

    public function telaEstoqueNovo($id)
    {
        $insumo = $this->insumoModel::where('id_insumo', $id)
                         ->where('id_usuario', Auth::id())
                         ->firstOrFail();

        if (request()->expectsJson()) {
            $html = view('insumos.estoque-novo', compact('insumo'))->render();
            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        }

        return view('insumos.estoque-novo', compact('insumo'));
    }

    public function telaAplicacao($id)
    {
        $aplicacao = $this->insumoModel->where('id_insumo', $id)
                          ->where('id_usuario', Auth::id())
                          ->with(['getAplicacoes' => function($query) {
                              $query->orderBy('dt_aplicacao', 'desc')
                                    ->orderBy('created_at', 'desc');
                          }])
                          ->firstOrFail();

        if (request()->expectsJson()) {
            $html = view('insumos.aplicacao', compact('aplicacao'))->render();
            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        }

        return view('insumos.aplicacao', compact('aplicacao'));
    }

    public function telaAplicacaoNova($id)
    {
        $aplicacao = $this->insumoModel::where('id_insumo', $id)
                          ->where('id_usuario', Auth::id())
                          ->firstOrFail();

        $lavouras = Lavoura::where('id_usuario', Auth::id())->get();

        if (request()->expectsJson()) {
            $html = view('insumos.aplicacao-nova', compact('aplicacao', 'lavouras'))->render();
            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        }

        return view('insumos.aplicacao-nova', compact('aplicacao', 'lavouras'));
    }

    public function telaRelatorio($id)
    {
        return $this->handleCustomFunction('relatorio', $id);
    }

}
