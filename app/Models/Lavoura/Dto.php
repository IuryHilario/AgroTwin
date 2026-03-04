<?php

/**
 * Data Transfer Object (DTO) para a entidade Lavoura
 * Responsável por transferir dados entre processos, como entre a camada de modelo e a camada de visão (views)
 */

namespace App\Models\Lavoura;

use App\Entity\LavouraEntity;
use App\Entity\UsuarioEntity;

trait Dto
{
    public static function getById($id)
    {
        return LavouraEntity::getLavouraById(self::query(), $id)->first();
    }

    public function getDetalhesFormatados()
    {
        $detalhes = [];

        $detalhes['id'] = $this->id_lavoura;
        $detalhes['cultura'] = $this->ds_cultura;
        $detalhes['dt_plantio'] = $this->dt_plantio;
        $detalhes['dt_colheita'] = $this->dt_colheita;
        $detalhes['status'] = $this->tp_status ? $this->tp_status->label() : 'Não informado';
        $detalhes['observacao'] = $this->ds_observacao ?? 'Nenhuma observação';
        $detalhes['propriedade'] = $this->propriedade ? $this->propriedade->ds_nome : 'Não informada';
        $detalhes['proprietario'] = UsuarioEntity::getNomeUsuarioById($this->id_usuario) ?? 'Não informado';
        $detalhes['data_criacao'] = $this->created_at;
        $detalhes['data_atualizacao'] = $this->updated_at;

        return $detalhes;
    }
}
