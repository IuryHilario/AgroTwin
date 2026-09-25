<?php

namespace App\Http\Controllers\Propriedade;

use App\Http\Requests\Propriedade\StorePropriedadeRequest;
use App\Services\ClimaService;
use Illuminate\Support\Facades\Auth;

trait Crud
{
    public function store(StorePropriedadeRequest $request)
    {
        $this->propriedadeModel->inserir($request->validated(), Auth::id());

        return redirect()->route('propriedade.index')
            ->with('success', 'Propriedade criada com sucesso!');
    }

    public function update(StorePropriedadeRequest $request, $id)
    {
        $propriedade = $this->buscarDoUsuario($this->model, $id);
        $propriedade->alterar(Auth::id(), $request->validated());

        return redirect()->route('propriedade.index')
            ->with('success', 'Propriedade atualizada com sucesso!');
    }

    // Mesma assinatura do show($id) de CrudOperations, por isso o service vem do container.
    public function show($id)
    {
        $propriedade = $this->buscarDoUsuario($this->model, $id);
        $clima = app(ClimaService::class)->daPropriedade($propriedade);

        return $this->responderView('propriedade.detalhar', compact('propriedade', 'clima'), $propriedade);
    }

    public function inativar($id)
    {
        $propriedade = $this->buscarDoUsuario($this->model, $id);
        $propriedade->alternarStatus();

        $mensagem = $propriedade->fl_inativo ? 'Inativado com Sucesso!!' : 'Ativado com Sucesso!!';

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => $mensagem]);
        }

        return redirect()->route('propriedade.index')->with('success', $mensagem);
    }
}
