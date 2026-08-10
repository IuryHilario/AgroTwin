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

    public function sensores()
    {
        return $this->hasMany(Sensor::class, 'id_lavoura', 'id_lavoura');
    }

    public function historicoIrrigacao()
    {
        return $this->hasMany(HistoricoIrrigacao::class, 'id_lavoura', 'id_lavoura');
    }

    public function recomendacoes()
    {
        return $this->hasMany(Recomendacao::class, 'id_lavoura', 'id_lavoura');
    }
}
