<?php

namespace App\Models\Insumo;

trait Core
{
    public function setFuncionalidades()
    {
        $funcionalidades = [];

        $funcVisualizar = [
            'id' => 'detalhar',
            'nome' => 'Detalhar',
            'icone' => 'fa-eye',
            'link' => route('insumos.show', $this->id_insumo),
        ];
        $funcionalidades[] = $funcVisualizar;

        $funcEditar = [
            'id' => 'editar',
            'nome' => 'Editar',
            'icone' => 'fa-edit',
            'link' => route('insumos.edit', $this->id_insumo),
        ];
        $funcionalidades[] = $funcEditar;

        $funcEstoque = [
            'id' => 'estoque',
            'nome' => 'Estoque',
            'icone' => 'fa-boxes',
            'link' => route('insumos.estoque', $this->id_insumo),
        ];
        $funcionalidades[] = $funcEstoque;

        $funcAplicacao = [
            'id' => 'aplicacao',
            'nome' => 'Aplicação',
            'icone' => 'fa-spray-can',
            'link' => route('insumos.aplicacao', $this->id_insumo),
        ];
        $funcionalidades[] = $funcAplicacao;

        $funcRelatorio = [
            'id' => 'relatorio',
            'nome' => 'Relatório',
            'icone' => 'fa-file-alt',
            'link' => route('insumos.relatorio', $this->id_insumo),
        ];
        $funcionalidades[] = $funcRelatorio;

        return $funcionalidades;
    }
}
