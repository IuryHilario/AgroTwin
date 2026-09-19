<?php

namespace App\Http\Controllers\Insumo;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use App\Http\Requests\Insumo\StoreInsumoRequest;
use App\Http\Requests\Insumo\StoreAplicacaoRequest;
use App\Http\Requests\Insumo\UpdateInsumoRequest;
use App\Http\Requests\Insumo\StoreEstoqueRequest;
use App\Mail\RelatorioInsumoMail;
use App\Models\InsumoControleEstoque;
use App\Services\InsumoRelatorioService;
use Barryvdh\DomPDF\Facade\Pdf;

trait Crud
{
    public function store(StoreInsumoRequest $request)
    {
        $this->insumoModel->inserir($request->validated(), Auth::id());

        return redirect()->route('insumos.index')->with('success', 'Insumo criado com sucesso!');
    }

    public function update(UpdateInsumoRequest $request, $id)
    {
        $this->insumoModel->alterar($id, $request->validated());

        return redirect()->route('insumos.index')
                        ->with('success', 'Insumo atualizado com sucesso!');
    }

    public function storeEstoque(StoreEstoqueRequest $request, $id)
    {
        $insumo = $this->insumoModel::where('id_insumo', $id)
                         ->where('id_usuario', Auth::id())
                         ->firstOrFail();

        $data = $request->validated();

        $data['id_insumo'] = $insumo->id_insumo;
        $this->insumoModel->inserirEstoque($data);

        return redirect()->route('insumos.estoque', $insumo->id_insumo)
                         ->with('success', 'Movimentação de estoque registrada com sucesso!');
    }

    public function storeAplicacao(StoreAplicacaoRequest $request, $id)
    {
        $insumo = $this->insumoModel::where('id_insumo', $id)
                         ->where('id_usuario', Auth::id())
                         ->firstOrFail();

        $data = $request->validated();

        $data['id_insumo'] = $insumo->id_insumo;
        $this->insumoModel->inserirAplicacao($data);

        return redirect()->route('insumos.aplicacao', $insumo->id_insumo)
                         ->with('success', 'Aplicação registrada com sucesso!');
    }

    /**
     * Envia o relatório do insumo (PDF em anexo) pro email do usuário logado.
     */
    public function enviarRelatorioEmail($id, InsumoRelatorioService $relatorioService)
    {
        $insumo = $this->insumoModel::where('id_insumo', $id)
                         ->where('id_usuario', Auth::id())
                         ->firstOrFail();

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
