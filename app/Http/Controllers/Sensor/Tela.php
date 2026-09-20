<?php

namespace App\Http\Controllers\Sensor;

use App\Entity\PropriedadeEntity;
use App\Models\Lavoura;
use App\Models\Propriedade;

trait Tela
{
    public function telaInserir()
    {
        return view('sensores.inserir', $this->opcoesDoFormulario());
    }

    public function telaAlterar($id)
    {
        $sensor = $this->buscarDoUsuario($this->model, $id);

        return view('sensores.edit', ['sensor' => $sensor] + $this->opcoesDoFormulario());
    }

    /** Propriedades e lavouras ativas do usuário, para os selects do formulário. */
    private function opcoesDoFormulario(): array
    {
        return [
            'propriedades' => PropriedadeEntity::pluckNomeByUsuario(Propriedade::query(), $this->idUsuario),
            'lavouras' => Lavoura::whereHas('propriedade', function ($query) {
                $query->where('id_usuario', $this->idUsuario)->where('fl_inativo', false);
            })->get(['id_lavoura', 'ds_cultura', 'id_propriedade']),
        ];
    }
}
