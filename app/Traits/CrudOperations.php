<?php

namespace App\Traits;

trait CrudOperations
{
    /**
     * Detalhe do registro (página ou modal via AJAX).
     *
     * A consulta já nasce restrita ao usuário logado (ver EscopoDoUsuario),
     * então um id de outro produtor resulta em 404 — não existe aqui uma
     * segunda checagem de permissão sobre o resultado.
     */
    public function show($id)
    {
        if (!isset($this->model) || !isset($this->resourceName) || !isset($this->singular)) {
            abort(404, 'Método show não implementado para este controller.');
        }

        $item = $this->buscarDoUsuario($this->model, $id);

        return $this->responderView($this->resourceName . '.detalhar', [$this->singular => $item], $item);
    }
}
