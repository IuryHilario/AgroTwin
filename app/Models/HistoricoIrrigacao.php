<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Entity\HistoricoIrrigacaoEntity;
use App\Traits\UsesEntity;

class HistoricoIrrigacao extends Model
{
    use UsesEntity;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setEntity(HistoricoIrrigacaoEntity::class);
    }

    public function lavoura()
    {
        return $this->belongsTo(Lavoura::class, 'id_lavoura', 'id_lavoura');
    }

    public function getDuracaoFormatada(): ?string
    {
        if (!$this->dt_fim) {
            return null;
        }

        $segundos = (int) abs($this->dt_inicio->diffInSeconds($this->dt_fim));

        if ($segundos < 60) {
            return "{$segundos} s";
        }

        $minutos = intdiv($segundos, 60);

        return $minutos < 60
            ? "{$minutos} min"
            : sprintf('%dh%02dmin', intdiv($minutos, 60), $minutos % 60);
    }
}
