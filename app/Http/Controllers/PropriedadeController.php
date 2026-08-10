<?php

namespace App\Http\Controllers;

use App\Models\Propriedade as PropriedadeModel;

class PropriedadeController extends Controller
{
    use Propriedade\Tela;
    use Propriedade\Crud;

    protected $model = PropriedadeModel::class;
    protected $resourceName = 'propriedade';
    protected $propriedadeModel;

    public function __construct()
    {
        $this->propriedadeModel = new PropriedadeModel();
    }
}
