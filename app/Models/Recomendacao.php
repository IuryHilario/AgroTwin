<?php

namespace App\Models;

use App\Entity\RecomendacaoEntity;
use App\Traits\UsesEntity;
use Illuminate\Database\Eloquent\Model;

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

    public static function doUsuario($idUsuario)
    {
        return RecomendacaoEntity::getPorUsuario((new self)->newQuery(), $idUsuario)->get();
    }

    public function setFuncionalidades()
    {
        return [
            [
                'id' => 'detalhar',
                'nome' => 'Detalhar',
                'icone' => 'fa-eye',
                'link' => route('recomendacoes.show', $this->id_recomendacao),
            ],
        ];
    }

    /**
     * Explica por que a recomendação foi gerada: o valor lido e o limite
     * configurado para a lavoura no momento (min/max), ou null quando o
     * registro é antigo e não guardou esses dados.
     */
    public function motivo(): ?string
    {
        if ($this->nu_valor_leitura === null) {
            return null;
        }

        $unidade = $this->tp_sensor?->unidade() ?? '';
        $valor = $this->nu_valor_leitura.$unidade;

        if ($this->nu_limite_min !== null && $this->nu_valor_leitura < $this->nu_limite_min) {
            return "Valor lido ({$valor}) abaixo do mínimo configurado ({$this->nu_limite_min}{$unidade}).";
        }

        if ($this->nu_limite_max !== null && $this->nu_valor_leitura > $this->nu_limite_max) {
            return "Valor lido ({$valor}) acima do máximo configurado ({$this->nu_limite_max}{$unidade}).";
        }

        return null;
    }
}
