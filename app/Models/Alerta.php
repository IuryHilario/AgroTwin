<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Entity\AlertaEntity;
use App\Traits\UsesEntity;

class Alerta extends Model
{
    use UsesEntity;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setEntity(AlertaEntity::class);
    }

    public function sensor()
    {
        return $this->belongsTo(Sensor::class, 'id_sensor', 'id_sensor');
    }

    public function lavoura()
    {
        return $this->belongsTo(Lavoura::class, 'id_lavoura', 'id_lavoura');
    }

    public static function doUsuario($idUsuario)
    {
        return AlertaEntity::getPorUsuario((new self())->newQuery(), $idUsuario)->get();
    }

    public function marcarComoLido(): void
    {
        $this->update(['fl_lida' => true]);
    }

    public function setFuncionalidades()
    {
        $funcionalidades = [];

        if (!$this->fl_lida) {
            $funcionalidades[] = [
                'id' => 'marcar-lido',
                'nome' => 'Marcar como lido',
                'icone' => 'fa-check',
                'link' => route('alertas.marcarLido', $this->id_alerta),
            ];
        }

        return $funcionalidades;
    }
}
