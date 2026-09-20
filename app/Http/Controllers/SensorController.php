<?php

namespace App\Http\Controllers;

use App\Enums\TipoStatusSensor;
use App\Models\Sensor as SensorModel;
use Illuminate\Support\Facades\Auth;

class SensorController extends Controller
{
    use Sensor\Tela;
    use Sensor\Crud;

    protected $model = SensorModel::class;
    protected $resourceName = 'sensores';
    protected $singular = 'sensor';
    protected $eagerLoad = ['lavoura', 'ultimaLeitura'];
    protected $colunasBusca = ['ds_nome'];
    protected $SensorModel;
    protected $idUsuario;

    public function __construct()
    {
        $this->SensorModel = new SensorModel();
        $this->idUsuario = Auth::id();
    }

    protected function estatisticas(): array
    {
        $sensores = $this->consultaDoUsuario($this->model)->with('ultimaLeitura')->get();

        return [
            ['valor' => $sensores->count(), 'rotulo' => 'sensores'],
            ['valor' => $sensores->where('ds_status', TipoStatusSensor::ATIVO)->count(), 'rotulo' => 'ativos'],
            ['valor' => $sensores->filter(fn (SensorModel $sensor) => !$sensor->estaOnline())->count(), 'rotulo' => 'sem enviar', 'destaque' => true],
        ];
    }
}
