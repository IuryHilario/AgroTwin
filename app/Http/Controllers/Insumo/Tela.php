<?php

namespace App\Http\Controllers\Insumo;

use App\Enums\TipoInsumo;
use App\Enums\TipoUnidadeMedida;
use App\Models\Lavoura;
use App\Services\InsumoRelatorioService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

trait Tela
{
    public function telaInserir()
    {
        return view('insumos.inserir', [
            'tiposInsumo' => TipoInsumo::cases(),
            'unidadesMedida' => TipoUnidadeMedida::cases(),
        ]);
    }

    public function telaEditar($id)
    {
        return view('insumos.edit', ['insumo' => $this->buscarDoUsuario($this->model, $id)]);
    }

    public function telaEstoque($id)
    {
        $insumo = $this->consultaDoUsuario($this->model)
            ->with(['movimentacoes' => function ($query) {
                $query->orderBy('dt_movimentacao', 'desc')
                      ->orderBy('created_at', 'desc')
                      ->limit(10);
            }])
            ->where('id_insumo', $id)
            ->firstOrFail();

        return $this->responderView('insumos.estoque', compact('insumo'), $insumo);
    }

    public function telaEstoqueNovo($id)
    {
        $insumo = $this->buscarDoUsuario($this->model, $id);

        return $this->responderView('insumos.estoque-novo', compact('insumo'), $insumo);
    }

    public function telaAplicacao($id)
    {
        $insumo = $this->consultaDoUsuario($this->model)
            ->with(['getAplicacoes' => function ($query) {
                $query->orderBy('dt_aplicacao', 'desc')->orderBy('created_at', 'desc');
            }])
            ->where('id_insumo', $id)
            ->firstOrFail();

        return $this->responderView('insumos.aplicacao', compact('insumo'), $insumo);
    }

    public function telaAplicacaoNova($id)
    {
        $insumo = $this->buscarDoUsuario($this->model, $id);
        $lavouras = Lavoura::where('id_usuario', Auth::id())->get();

        return $this->responderView('insumos.aplicacao-nova', compact('insumo', 'lavouras'), $insumo);
    }

    public function telaRelatorio($id, InsumoRelatorioService $relatorioService)
    {
        [$insumo, $relatorio] = $this->buscarRelatorio($id, $relatorioService);

        return $this->responderView('insumos.relatorio', compact('insumo', 'relatorio'), $insumo);
    }

    public function baixarRelatorioPdf($id, InsumoRelatorioService $relatorioService)
    {
        [$insumo, $relatorio] = $this->buscarRelatorio($id, $relatorioService);

        return Pdf::loadView('insumos.relatorio-pdf', compact('insumo', 'relatorio'))
            ->download("relatorio-{$insumo->ds_nome}.pdf");
    }

    private function buscarRelatorio($id, InsumoRelatorioService $relatorioService): array
    {
        $insumo = $this->buscarDoUsuario($this->model, $id);

        return [$insumo, $relatorioService->gerar($insumo)];
    }
}
