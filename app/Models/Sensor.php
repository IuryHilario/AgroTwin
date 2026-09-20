<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Entity\SensorEntity;
use App\Models\Propriedade;
use App\Models\Lavoura;
use App\Traits\UsesEntity;

class Sensor extends Model
{
    use HasFactory, UsesEntity;
    use Sensor\Core;
    use Sensor\Insert;
    use Sensor\Update;
    use Sensor\Dto;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setEntity(SensorEntity::class);
    }

    public function propriedade()
    {
        return $this->belongsTo(Propriedade::class, 'id_propriedade', 'id_propriedade');
    }

    public function lavoura()
    {
        return $this->belongsTo(Lavoura::class, 'id_lavoura', 'id_lavoura');
    }

    public function leituras()
    {
        return $this->hasMany(LeituraSensor::class, 'id_sensor', 'id_sensor');
    }

    /** Leitura mais recente, carregada junto da listagem para mostrar se o sensor ainda envia. */
    public function ultimaLeitura()
    {
        return $this->hasOne(LeituraSensor::class, 'id_sensor', 'id_sensor')->latestOfMany('dt_leitura');
    }

    /**
     * Tolerância antes de considerar o sensor mudo: quatro vezes o intervalo
     * de leitura configurado na lavoura (uma falha isolada de envio não deve
     * acender o alerta), com um piso de uma hora.
     */
    public function minutosDeTolerancia(): int
    {
        return max(60, ($this->lavoura?->nu_intervalo_leitura_minutos ?? 15) * 4);
    }

    public function estaOnline(): bool
    {
        $ultima = $this->ultimaLeitura;

        return $ultima !== null && $ultima->dt_leitura->diffInMinutes(now()) <= $this->minutosDeTolerancia();
    }

}
