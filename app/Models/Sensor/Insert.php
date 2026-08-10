<?php

/**
 * Área exclusiva para inserção de dados, sendo o Insert do Model
 */

namespace App\Models\Sensor;

use App\Services\BaseService;
use Illuminate\Support\Str;

trait Insert
{
    public function inserir(array $data, $usuarioId)
    {
        $data['ds_status'] = $data['ds_status'] ?? 'ativo';
        $data['token'] = Str::random(40);

        $service = new BaseService($this);
        return $service->_inserir($data);
    }
}
