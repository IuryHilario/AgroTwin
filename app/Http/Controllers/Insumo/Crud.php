<?php

namespace App\Http\Controllers\Insumo;

use App\Http\Requests\Insumo\StoreAplicacaoRequest;
use App\Http\Requests\Insumo\StoreEstoqueRequest;
use App\Http\Requests\Insumo\StoreInsumoRequest;
use App\Http\Requests\Insumo\UpdateInsumoRequest;
use App\Mail\RelatorioInsumoMail;
use App\Services\InsumoRelatorioService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

trait Crud
{
    public function store(StoreInsumoRequest $request)
    {
        $this->insumoModel->inserir($request->validated(), Auth::id());

        return redirect()->route('insumos.index')->with('success', 'Insumo criado com sucesso!');
    }

    public function update(UpdateInsumoRequest $request, $id)
    {
        $insumo = $this->buscarDoUsuario($this->model, $id);
        $insumo->update($request->validated());

        return redirect()->route('insumos.index')
                        ->with('success', 'Insumo atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $insumo = $this->buscarDoUsuario($this->model, $id);
        $insumo->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Insumo excluído com sucesso!']);
        }

        return redirect()->route('insumos.index')->with('success', 'Insumo excluído com sucesso!');
    }

    public function storeEstoque(StoreEstoqueRequest $request, $id)
    {
        $insumo = $this->buscarDoUsuario($this->model, $id);

        $this->insumoModel->inserirEstoque($request->validated() + ['id_insumo' => $insumo->id_insumo]);

        return redirect()->route('insumos.estoque', $insumo->id_insumo)
                         ->with('success', 'Movimentação de estoque registrada com sucesso!');
    }

    public function storeAplicacao(StoreAplicacaoRequest $request, $id)
    {
        $insumo = $this->buscarDoUsuario($this->model, $id);

        $this->insumoModel->inserirAplicacao($request->validated() + ['id_insumo' => $insumo->id_insumo]);

        return redirect()->route('insumos.aplicacao', $insumo->id_insumo)
                         ->with('success', 'Aplicação registrada com sucesso!');
    }

    /**
     * Envia o relatório do insumo (PDF em anexo) pro email do usuário logado.
     */
    public function enviarRelatorioEmail($id, InsumoRelatorioService $relatorioService)
    {
        $insumo = $this->buscarDoUsuario($this->model, $id);
        $relatorio = $relatorioService->gerar($insumo);
        $pdf = Pdf::loadView('insumos.relatorio-pdf', compact('insumo', 'relatorio'))->output();

        $usuario = Auth::user();
        Mail::to($usuario->email)->send(new RelatorioInsumoMail($insumo, $relatorio, $pdf));

        return response()->json([
            'success' => true,
            'message' => "Relatório enviado para {$usuario->email}.",
        ]);
    }
}
