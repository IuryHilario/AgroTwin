<?php

namespace app\Models\Insumo;

use App\Services\BaseService;

trait Insert
{
    public function inserir(array $data, $usuarioId)
    {
        $data['id_usuario'] = $usuarioId;

        $service = new BaseService($this);
        return $service->_inserir($data);
    }

    public function inserirEstoque(array $data)
    {
        $service = new BaseService(new \App\Models\InsumoControleEstoque());
        return $service->_inserir($data);
    }

    public function inserirAplicacao(array $data)
    {
        $service = new BaseService(new \App\Models\InsumoAplicacao());
        return $service->_inserir($data);
    }
}
