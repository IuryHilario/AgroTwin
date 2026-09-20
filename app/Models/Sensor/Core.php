<?php

/**
 * Área exclusiva para funcionalidades, sendo o Core do Model
 */

namespace App\Models\Sensor;

trait Core
{
    public function setFuncionalidades()
    {
        $funcionalidades = [];

        $funcVisualizar = [
            'id' => 'detalhar',
            'nome' => 'Detalhar',
            'icone' => 'fa-eye',
            'link' => route('sensores.show', $this->id_sensor),
        ];
        $funcionalidades[] = $funcVisualizar;

        $funcEditar = [
            'id' => 'editar',
            'nome' => 'Editar',
            'icone' => 'fa-edit',
            'link' => route('sensores.edit', $this->id_sensor),
        ];
        $funcionalidades[] = $funcEditar;

        $funcExcluir = [
            'id' => 'excluir',
            'nome' => 'Excluir',
            'icone' => 'fa-trash',
            'link' => route('sensores.destroy', $this->id_sensor),
        ];
        $funcionalidades[] = $funcExcluir;

        return $funcionalidades;
    }
}
