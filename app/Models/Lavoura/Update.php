<?php

/**
 * Área exclusiva para atualização de dados, sendo o Update do Model
 */

namespace App\Models\Lavoura;

use App\Services\BaseService;
use Illuminate\Support\Str;

trait Update
{
    public function alterar($usuarioId, array $data)
    {
        $data['id_usuario'] = $usuarioId;

        // Lavouras criadas antes do recurso de irrigação não têm token — gera na primeira edição.
        if (!$this->token_irrigacao) {
            $data['token_irrigacao'] = Str::random(40);
        }

        $service = new BaseService($this);
        return $service->_alterar($this, $data);
    }
}
