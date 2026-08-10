<?php

namespace App\Http\Controllers;

use App\Models\Sensor as SensorModel;
use Illuminate\Support\Facades\Auth;

class SensorController extends Controller
{
    use Sensor\Tela;
    use Sensor\Crud;

    protected $model = SensorModel::class;
    protected $resourceName = 'sensores';
    protected $SensorModel;
    protected $idUsuario;

    public function __construct()
    {
        $this->SensorModel = new SensorModel();
        $this->idUsuario = Auth::id();
    }
}
