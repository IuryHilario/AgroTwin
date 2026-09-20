<?php

namespace App\Http\Controllers;

use App\Models\Propriedade as PropriedadeModel;

class PropriedadeController extends Controller
{
    use Propriedade\Tela;
    use Propriedade\Crud;

    protected $model = PropriedadeModel::class;
    protected $resourceName = 'propriedade';
    protected $singular = 'propriedade';
    protected $colunasBusca = ['ds_nome', 'ds_localizacao'];
    protected $propriedadeModel;

    public function __construct()
    {
        $this->propriedadeModel = new PropriedadeModel();
    }

    protected function estatisticas(): array
    {
        $total = $this->consultaDoUsuario($this->model)->count();

        return [
            ['valor' => $total, 'rotulo' => $total === 1 ? 'cadastrada' : 'cadastradas'],
            ['valor' => $this->consultaDoUsuario($this->model)->where('fl_inativo', false)->count(), 'rotulo' => 'ativas', 'destaque' => true],
        ];
    }
}
