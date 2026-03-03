<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class BaseService
{
    protected Model $negocio;

    public function __construct(Model $negocio)
    {
        $this->negocio = $negocio;
    }

    public function all()
    {
        return $this->negocio->all();
    }

    public function find(int $id)
    {
        return $this->negocio->findOrFail($id);
    }

    public function _inserir(array $data)
    {

        return $this->negocio->create($data);
    }

    public function _alterar(Model $negocio, array $data)
    {
        $negocio->update($data);
        return $negocio;
    }

    public function delete(Model $negocio): bool
    {
        return $negocio->delete();
    }
}
