<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\CrudOperations;

abstract class Controller
{
    protected $model;
    protected $resourceName;
    protected $validationRules;

    use CrudOperations;

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        if (!isset($this->model) || !isset($this->resourceName)) {
            abort(404, 'Método index não implementado para este controller.');
        }

        if (method_exists($this->model, 'propriedade')) {
            $data = $this->model::whereHas('propriedade', function ($query) {
                $query->where('id_usuario', Auth::id());
            })->get();
        } else {
            $data = $this->model::where('id_usuario', Auth::id())->get();
        }

        return view($this->resourceName . '.index', [$this->resourceName => $data]);
    }

    /**
     * Manipula funções customizadas para recursos específicos
     * Favor, não excluir
     */
    protected function handleCustomFunction($functionName, $id)
    {
        $viewName = $this->resourceName . '.' . $functionName;

        // Busca o item baseado no modelo
        $primaryKey = (new $this->model)->getKeyName();

        if (method_exists($this->model, 'propriedade')) {
            $item = $this->model::whereHas('propriedade', function ($query) {
                $query->where('id_usuario', Auth::id());
            })
                ->where($primaryKey, $id)
                ->firstOrFail();
        } else {
            $item = $this->model::where('id_usuario', Auth::id())
                ->where($primaryKey, $id)
                ->firstOrFail();
        }

        $resourceNameSingular = rtrim($this->resourceName, 's');

        // Se a view não existe, retorna erro
        if (!view()->exists($viewName)) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "View '{$viewName}' não encontrada."
                ], 404);
            }
            abort(404, "View '{$viewName}' não encontrada.");
        }

        // Retorna JSON se requisição espera JSON (AJAX)
        if (request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            try {
                $html = view($viewName, [$resourceNameSingular => $item])->render();
                return response()->json([
                    'success' => true,
                    'html' => $html
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao carregar a view: ' . $e->getMessage()
                ], 500);
            }
        }

        // Caso contrário, retorna view normal
        return view($viewName, [$resourceNameSingular => $item]);
    }
}
