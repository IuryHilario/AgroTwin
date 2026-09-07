<?php

namespace App\Http\Controllers\Lavoura;

use App\Http\Requests\Lavoura\StoreLavouraRequest;
use App\Http\Requests\Lavoura\UpdateLavouraRequest;

trait Crud
{
    public function store(StoreLavouraRequest $request)
    {
        $this->LavouraModel->inserir($request->validated(), $this->idUsuario);

        return redirect()->route('lavouras.index')
            ->with('success', 'Lavoura inserida com sucesso!');
    }

    public function update(UpdateLavouraRequest $request, $id)
    {
        $lavoura = $this->LavouraModel::findOrFail($id);
        $lavoura->alterar($this->idUsuario, $request->validated());

        return redirect()->route('lavouras.index')
            ->with('success', 'Lavoura atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $lavoura = $this->LavouraModel::whereHas('propriedade', function ($query) {
            $query->where('id_usuario', $this->idUsuario);
        })->findOrFail($id);

        $lavoura->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Excluído com Sucesso!!']);
        }

        return redirect()->route('lavouras.index')
            ->with('success', 'Lavoura excluída com sucesso!');
    }
}
