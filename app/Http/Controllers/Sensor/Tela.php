<?php

namespace App\Http\Controllers\Sensor;

trait Tela
{
    public function telaInserir()
    {
        return view('sensores.inserir');
    }
}