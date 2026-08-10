<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\UsesEntity;
use App\Entity\InsumoAplicacaoEntity;

class InsumoAplicacao extends Model
{
    use UsesEntity;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setEntity(InsumoAplicacaoEntity::class);
    }

    public function lavoura()
    {
        return $this->belongsTo(Lavoura::class, 'id_lavoura', 'id_lavoura');
    }
}
