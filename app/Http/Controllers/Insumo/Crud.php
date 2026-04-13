<?php

namespace App\Http\Controllers\Insumo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\Insumo\StoreInsumoRequest;
use App\Http\Requests\Insumo\StoreAplicacaoRequest;
use App\Http\Requests\Insumo\StoreEstoqueRequest;

use App\Models\InsumoControleEstoque;

trait Crud
{
    public function store(StoreInsumoRequest $request)
    {
        $this->insumoModel->inserir($request->validated(), Auth::id());

        return redirect()->route('insumos.index')->with('success', 'Insumo criado com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $arValores = [];
        foreach ($request->all() as $key => $value) {
            if (is_string($value)) {
                $arValores[$key] = trim($value);
            }
        }
        $insumo = $this->insumoModel::findOrFail($id);
        $insumo->alterar($id, $arValores);

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
}
