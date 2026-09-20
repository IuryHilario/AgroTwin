<?php

namespace App\Http\Controllers;

use App\Traits\CrudOperations;
use App\Traits\EscopoDoUsuario;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class Controller
{
    use CrudOperations;
    use EscopoDoUsuario;

    /** Itens por página nas listagens. */
    protected const POR_PAGINA = 15;

    protected $model;

    /** Nome do recurso: pasta das views, prefixo das rotas e chave da variável na listagem. */
    protected $resourceName;

    /**
     * Nome no singular da variável passada às views de detalhe/edição.
     * Precisa ser explícito: "sensores" e "recomendacoes" não viram
     * "sensor"/"recomendacao" com nenhuma regra automática de plural.
     */
    protected $singular;

    /** Relações carregadas junto da listagem, para não fazer uma query por linha. */
    protected $eagerLoad = [];

    /** Colunas varridas pelo campo de busca da listagem. */
    protected $colunasBusca = [];

    protected $validationRules;

    public function index(Request $request)
    {
        if (!isset($this->model) || !isset($this->resourceName)) {
            abort(404, 'Método index não implementado para este controller.');
        }

        $busca = trim((string) $request->input('busca'));

        $consulta = $this->consultaDoUsuario($this->model)->with($this->eagerLoad);
        $this->aplicarBusca($consulta, $busca);

        $registros = $consulta->paginate(static::POR_PAGINA)->withQueryString();

        return view($this->resourceName . '.index', [
            $this->resourceName => $registros,
            'busca' => $busca,
            'stats' => $this->estatisticas(),
        ]);
    }

    protected function aplicarBusca(Builder $consulta, string $busca): void
    {
        if ($busca === '' || empty($this->colunasBusca)) {
            return;
        }

        $consulta->where(function (Builder $query) use ($busca) {
            foreach ($this->colunasBusca as $coluna) {
                $query->orWhere($coluna, 'like', "%{$busca}%");
            }
        });
    }

    /**
     * Números do cabeçalho da listagem. Ficam aqui (e não na view) porque com
     * paginação a coleção só tem a página atual — contar nela daria o número
     * errado. Cada controller devolve os seus.
     */
    protected function estatisticas(): array
    {
        return [];
    }

    /**
     * Abre uma view auxiliar do recurso (estoque, aplicação…) para um registro
     * do usuário, respondendo HTML na navegação normal e JSON no AJAX.
     */
    protected function handleCustomFunction($functionName, $id)
    {
        $viewName = $this->resourceName . '.' . $functionName;

        if (!view()->exists($viewName)) {
            abort(404, "View '{$viewName}' não encontrada.");
        }

        $item = $this->buscarDoUsuario($this->model, $id);

        return $this->responderView($viewName, [$this->singular => $item], $item);
    }

    /**
     * Devolve a view renderizada dentro de um JSON quando a requisição é AJAX
     * (é o formato que os modais do sistema esperam).
     *
     * Na navegação normal, a mesma view vai dentro do layout: essas telas são
     * componentes de modal, e servi-las cruas deixava a página sem estilo
     * nenhum quando alguém abria a URL direto.
     */
    protected function responderView(string $view, array $dados, $item = null)
    {
        $html = view($view, $dados)->render();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $item,
                'html' => $html,
            ]);
        }

        return view('layouts.pagina-modal', [
            'conteudo' => $html,
            'titulo' => ucfirst($this->resourceName) . ' - AgroTwin',
            'voltarPara' => url()->previous() !== url()->current()
                ? url()->previous()
                : route($this->resourceName . '.index'),
        ]);
    }
}
