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

}
