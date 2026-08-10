<?php

/**
 * Área exclusiva para atualização de dados, sendo o Update do Model
 */

namespace App\Models\Sensor;

use App\Services\BaseService;

trait Update
{
    public function alterar($usuarioId, array $data)
    {
        $service = new BaseService($this);
        return $service->_alterar($this, $data);
    }
}
