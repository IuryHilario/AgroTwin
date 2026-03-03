<?php

namespace App\Http\Controllers;

use App\Models\Lavoura as LavouraModel;
use Illuminate\Support\Facades\Auth;

class LavouraController extends Controller
{
    use Lavoura\Tela;
    use Lavoura\Crud;

    protected $model = LavouraModel::class;
    protected $resourceName = 'lavouras';
    protected $LavouraModel;
    protected $idUsuario;

    public function __construct()
    {
        $this->LavouraModel = new LavouraModel();
        $this->idUsuario = Auth::id();
    }
}
