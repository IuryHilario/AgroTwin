<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesEntity;
use App\Entity\InsumoControleEstoqueEntity;
use App\Enums\TipoMovimentacao;

class InsumoControleEstoque extends Model
{
    use UsesEntity;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setEntity(InsumoControleEstoqueEntity::class);
    }

    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'id_insumo', 'id_insumo');
    }

    public function scopeTipoMovimentacao($query, $tipo)
    {
        return $query->where('tp_controle', $tipo);
    }

    public function scopeOrdenadoPorData($query, $direcao = 'desc')
    {
        return $query->orderBy('dt_movimentacao', $direcao);
    }

    public function getQuantidadeFormatadaAttribute()
    {
        $sinal = $this->tp_controle === TipoMovimentacao::ENTRADA ? '+' : '-';
        return $sinal . number_format($this->nu_quantidade, 2, ',', '.');
    }

    public function getTipoClassAttribute()
    {
        return $this->tp_controle === TipoMovimentacao::ENTRADA ? 'success' : 'danger';
    }
}
