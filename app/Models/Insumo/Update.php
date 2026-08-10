<?php

namespace app\Models\Insumo;

use Illuminate\Support\Facades\Auth;

use App\Services\BaseService;

trait Update
{
    public function alterar($insumoId, array $data)
    {
        $insumo = $this->newQuery()
            ->where('id_insumo', $insumoId)
            ->where('id_usuario', Auth::id())
            ->firstOrFail();

        $service = new BaseService($insumo);
        return $service->_alterar($insumo, $data);
    }
}
