<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Entity\SensorEntity;
use App\Models\Propriedade;
use App\Traits\UsesEntity;

class Sensor extends Model
{
    use HasFactory, UsesEntity;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setEntity(SensorEntity::class);
    }

    public function propriedade()
    {
        return $this->belongsTo(Propriedade::class, 'id_propriedade', 'id_propriedade');
    }

}
