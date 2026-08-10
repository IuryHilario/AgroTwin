<?php

/**
 * Data Transfer Object (DTO) para a entidade Sensor
 * Responsável por transferir dados entre processos, como entre a camada de modelo e a camada de visão (views)
 */

namespace App\Models\Sensor;

use App\Entity\SensorEntity;

trait Dto
{
    public static function getById($id)
    {
        return SensorEntity::getSensorById(self::query(), $id)->first();
    }

    public function getDetalhesFormatados()
    {
        $detalhes = [];

        $detalhes['id'] = $this->id_sensor;
        $detalhes['nome'] = $this->ds_nome;
        $detalhes['tipo'] = $this->tp_sensor ? $this->tp_sensor->label() : 'Não informado';
        $detalhes['unidade'] = $this->tp_sensor ? $this->tp_sensor->unidade() : '';
        $detalhes['status'] = $this->ds_status ? $this->ds_status->label() : 'Não informado';
        $detalhes['propriedade'] = $this->propriedade ? $this->propriedade->ds_nome : 'Não informada';
        $detalhes['lavoura'] = $this->lavoura ? $this->lavoura->ds_cultura : 'Não atribuído';
        $detalhes['token'] = $this->token;

        return $detalhes;
    }
}
