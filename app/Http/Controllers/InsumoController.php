<?php

namespace App\Http\Controllers;

use App\Models\Insumo as InsumoModel;

class InsumoController extends Controller
{
    use Insumo\Tela;
    use Insumo\Crud;

    protected $model = InsumoModel::class;
    protected $resourceName = 'insumos';

    protected $insumoModel;

    public function __construct()
    {
        $this->insumoModel = new InsumoModel();
    }

}
