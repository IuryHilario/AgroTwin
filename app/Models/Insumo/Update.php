<?php

namespace app\Models\Insumo;

use App\Services\BaseService;

trait Update
{
    public function alterar($usuarioId, array $data)
    {
        $data['id_usuario'] = $usuarioId;

        $service = new BaseService($this);
        return $service->_alterar($this, $data);
    }
}
