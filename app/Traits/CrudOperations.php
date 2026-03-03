<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


trait CrudOperations
{
    /** Exibe detalhes (modal ou página) */
    public function show($id)
    {
        if (!isset($this->model) || !isset($this->resourceName)) {
            abort(404, 'Método show não implementado para este controller.');
        }
        $item = null;
        $viewName = 'detalhar';
        $viewVariable = 'item';
        if (method_exists($this->model, 'getById')) {
            $item = $this->model::getById($id);
            $viewVariable = rtrim($this->resourceName, 's');
        } else {
            $primaryKey = (new $this->model)->getKeyName();
            if (method_exists($this->model, 'propriedade')) {
                $item = $this->model::whereHas('propriedade', function ($q) {
                    $q->where('id_usuario', Auth::id());
                })
                    ->where($primaryKey, $id)->firstOrFail();
            } else {
                $item = $this->model::where('id_usuario', Auth::id())
                    ->where($primaryKey, $id)->firstOrFail();
            }
        }
        if (!$item) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Registro não encontrado ou acesso negado.'], 404);
            }
            return redirect()->route($this->resourceName . '.index')->with('error', 'Registro não encontrado.');
        }
        $hasPermission = isset($item->id_usuario) ? $item->id_usuario === Auth::id() : (isset($item->propriedade->id_usuario) ? $item->propriedade->id_usuario === Auth::id() : true);
        if (!$hasPermission) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Acesso negado.'], 403);
            }
            return redirect()->route($this->resourceName . '.index')->with('error', 'Acesso negado.');
        }
        if (request()->ajax()) {
            $html = view($this->resourceName . '.' . $viewName, [$viewVariable => $item])->render();
            return response()->json(['success' => true, 'data' => $item, 'html' => $html]);
        }
        return view($this->resourceName . '.' . $viewName, [$viewVariable => $item]);
    }
}
