<?php

namespace App\Models\Insumo;

trait Dto
{
    public static function getById($id)
    {
        return self::with('usuario')->where('id_insumo', $id)->first();
    }

    public function getDetalhesFormatados()
    {
        $detalhes =  [
            'id' => $this->id_insumo,
            'nome' => $this->ds_nome,
            'tipo' => $this->tp_insumo ? $this->tp_insumo->label() : 'Não informado',
            'fabricante' => $this->ds_fabricante ?? 'Não informado',
            'unidade_medida' => $this->tp_unidade_medida ? $this->tp_unidade_medida->label() : 'Não informado',
            'data_validade' => $this->dt_validade,
            'composicao' => $this->ds_composicao ?? 'Não informada',
            'usuario' => $this->usuario ? $this->usuario->name : 'Não informado',
            'data_criacao' => $this->created_at,
            'data_atualizacao' => $this->updated_at,
        ];

        return $detalhes;
    }
}
