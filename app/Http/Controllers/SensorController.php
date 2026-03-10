<?php

namespace App\Http\Controllers;

use App\Models\Sensor as SensorModel;

class SensorController extends Controller
{
    use Sensor\Tela;

    protected $model = SensorModel::class;
    protected $resourceName = 'sensores';


}
