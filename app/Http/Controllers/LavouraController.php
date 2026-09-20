<?php

namespace App\Http\Controllers;

use App\Enums\TipoStatus;
use App\Models\Lavoura as LavouraModel;
use Illuminate\Support\Facades\Auth;

class LavouraController extends Controller
{
    use Lavoura\Tela;
    use Lavoura\Crud;
    use Lavoura\Limites;
    use Lavoura\Irrigacao;

    protected $model = LavouraModel::class;
    protected $resourceName = 'lavouras';
    protected $singular = 'lavoura';
    protected $eagerLoad = ['propriedade'];
    protected $colunasBusca = ['ds_cultura'];
    protected $LavouraModel;
    protected $idUsuario;

    public function __construct()
    {
        $this->LavouraModel = new LavouraModel();
        $this->idUsuario = Auth::id();
    }

    protected function estatisticas(): array
    {
        $consulta = fn () => $this->consultaDoUsuario($this->model);

        return [
            ['valor' => $consulta()->count(), 'rotulo' => 'lavouras'],
            ['valor' => $consulta()->where('tp_status', TipoStatus::ATIVO)->count(), 'rotulo' => 'em cultivo'],
            ['valor' => $consulta()->where('fl_irrigacao_ativa', true)->count(), 'rotulo' => 'irrigando', 'destaque' => true],
        ];
    }
}
