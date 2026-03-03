<?php

/**
 * Área exclusiva para inserção de dados, sendo o Insert do Model
 */

namespace App\Models\Lavoura;

use App\Services\BaseService;

trait Insert
{
    public function inserir(array $data, $usuarioId)
    {
        $data['id_usuario'] = $usuarioId;
        $data['tp_status'] = $data['tp_status'] ?? 'ativo';

        $service = new BaseService($this);
        return $service->_inserir($data);
    }
}
