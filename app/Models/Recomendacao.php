<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Entity\RecomendacaoEntity;
use App\Traits\UsesEntity;

class Recomendacao extends Model
{
    use UsesEntity;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setEntity(RecomendacaoEntity::class);
    }

    public function lavoura()
    {
        return $this->belongsTo(Lavoura::class, 'id_lavoura', 'id_lavoura');
    }
}
