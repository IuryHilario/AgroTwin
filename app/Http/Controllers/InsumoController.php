<?php

namespace App\Http\Controllers;

use App\Models\Insumo as InsumoModel;

class InsumoController extends Controller
{
    use Insumo\Tela;
    use Insumo\Crud;

    protected $model = InsumoModel::class;
    protected $resourceName = 'insumos';
    protected $singular = 'insumo';
    protected $colunasBusca = ['ds_nome', 'ds_fabricante'];
    protected $insumoModel;

    public function __construct()
    {
        $this->insumoModel = new InsumoModel();
    }

    protected function estatisticas(): array
    {
        $insumos = $this->consultaDoUsuario($this->model)->get();

        return [
            ['valor' => $insumos->count(), 'rotulo' => 'insumos'],
            ['valor' => $insumos->filter(fn (InsumoModel $insumo) => $insumo->venceEmBreve())->count(), 'rotulo' => 'vencem em 30d'],
            ['valor' => $insumos->filter(fn (InsumoModel $insumo) => $insumo->estoque_abaixo_minimo)->count(), 'rotulo' => 'estoque baixo', 'destaque' => true],
        ];
    }
}
