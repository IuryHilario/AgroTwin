<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesEntity;
use App\Entity\InsumoEntity;
use App\Models\Usuario;

class Insumo extends Model
{
    use UsesEntity;

    use Insumo\Core;
    use Insumo\Dto;
    use Insumo\Insert;
    use Insumo\Update;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setEntity(InsumoEntity::class);

    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function movimentacoes()
    {
        return $this->hasMany(InsumoControleEstoque::class, 'id_insumo', 'id_insumo');
    }

    public function getAplicacoes()
    {
        return $this->hasMany(InsumoAplicacao::class, 'id_insumo', 'id_insumo')
                    ->orderBy('id_insumo_aplicacao', 'desc');

    }

    public function getEstoqueAtualAttribute()
    {
        return $this->movimentacoes()
            ->selectRaw('
                SUM(CASE
                    WHEN tp_controle = "entrada" THEN nu_quantidade
                    WHEN tp_controle = "saida" THEN -nu_quantidade
                    ELSE 0
                END) as estoque_atual
            ')
            ->value('estoque_atual') ?? 0;
    }

    public function getEstoqueAbaixoMinimoAttribute()
    {
        return $this->estoque_atual < $this->nu_estoque_minimo;
    }

    public function getMovimentacoesComSaldoAttribute()
    {
        $movimentacoes = $this->movimentacoes()
            ->orderBy('dt_movimentacao', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $saldoAcumulado = 0;
        $movimentacoesComSaldo = collect();

        foreach ($movimentacoes as $movimentacao) {
            $quantidade = $movimentacao->tp_controle === \App\Enums\TipoMovimentacao::ENTRADA
                ? $movimentacao->nu_quantidade
                : -$movimentacao->nu_quantidade;

            $saldoAcumulado += $quantidade;

            $movimentacao->saldo_calculado = $saldoAcumulado;
            $movimentacoesComSaldo->push($movimentacao);
        }

        return $movimentacoesComSaldo->sortByDesc(function($mov) {
            return $mov->dt_movimentacao . ' ' . $mov->created_at;
        });
    }

}
