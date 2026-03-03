<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesEntity;
use App\Entity\LavouraEntity;
use App\Models\Propriedade;

class Lavoura extends Model
{
    use UsesEntity;
    use Lavoura\Core;
    use Lavoura\Insert;
    use Lavoura\Update;
    use Lavoura\Dto;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setEntity(LavouraEntity::class);
    }

    public function propriedade()
    {
        return $this->belongsTo(Propriedade::class, 'id_propriedade', 'id_propriedade');
    }
}
