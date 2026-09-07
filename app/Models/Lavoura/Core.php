<?php

/**
 * Área exclusiva para funcionalidades, sendo o Core do Model
 */

namespace App\Models\Lavoura;

trait Core
{
    public function setFuncionalidades()
    {
        $funcionalidades = [];

        $funcVisualizar = [
            'id' => 'detalhar',
            'nome' => 'Detalhar',
            'icone' => 'fa-eye',
            'link' => route('lavouras.show', $this->id_lavoura),
        ];
        $funcionalidades[] = $funcVisualizar;

        $funcEditar = [
            'id' => 'editar',
            'nome' => 'Editar',
            'icone' => 'fa-edit',
            'link' => route('lavouras.edit', $this->id_lavoura),
        ];
        $funcionalidades[] = $funcEditar;

        $funcMonitor = [
            'id' => 'monitorar',
            'nome' => 'Monitorar',
            'icone' => 'fa-desktop',
            'link' => route('lavouras.monitorar', $this->id_lavoura),
        ];
        $funcionalidades[] = $funcMonitor;

        $funcLimites = [
            'id' => 'limites',
            'nome' => 'Configurar Limites',
            'icone' => 'fa-sliders-h',
            'link' => route('lavouras.limites', $this->id_lavoura),
        ];
        $funcionalidades[] = $funcLimites;

        $funcExcluir = [
            'id' => 'excluir',
            'nome' => 'Excluir',
            'icone' => 'fa-trash',
            'link' => route('lavouras.destroy', $this->id_lavoura),
        ];
        $funcionalidades[] = $funcExcluir;

        return $funcionalidades;
    }
}
