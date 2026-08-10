<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Entity\LeituraSensorEntity;
use App\Traits\UsesEntity;

class LeituraSensor extends Model
{
    use UsesEntity;

    public $timestamps = true;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setEntity(LeituraSensorEntity::class);
    }

    public function sensor()
    {
        return $this->belongsTo(Sensor::class, 'id_sensor', 'id_sensor');
    }
}
