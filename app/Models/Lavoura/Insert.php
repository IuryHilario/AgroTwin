<?php

/**
 * Área exclusiva para inserção de dados, sendo o Insert do Model
 */

namespace App\Models\Lavoura;

use App\Services\BaseService;
use Illuminate\Support\Str;

trait Insert
{
    public function inserir(array $data, $usuarioId)
    {
        $data['id_usuario'] = $usuarioId;
        $data['tp_status'] = $data['tp_status'] ?? 'ativo';
        $data['token_irrigacao'] = Str::random(40);

        $service = new BaseService($this);
        return $service->_inserir($data);
    }
}
