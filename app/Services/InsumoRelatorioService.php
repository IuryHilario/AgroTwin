<?php

namespace App\Services;

use App\Enums\TipoMovimentacao;
use App\Models\Insumo;
use App\Models\InsumoAplicacao;
use App\Models\InsumoControleEstoque;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class InsumoRelatorioService
{
    /**
     * Consumo, custo e distribuição de um insumo nos últimos $dias dias,
     * a partir das aplicações e do custo médio de compra registrados.
     */
    public function gerar(Insumo $insumo, int $dias = 30): array
    {
        $fim = now()->endOfDay();
        $inicio = $fim->copy()->subDays($dias - 1)->startOfDay();

        $aplicacoes = InsumoAplicacao::where('id_insumo', $insumo->id_insumo)
            ->whereBetween('dt_aplicacao', [$inicio, $fim])
            ->with('lavoura')
            ->get();

        $custoMedioUnitario = InsumoControleEstoque::where('id_insumo', $insumo->id_insumo)
            ->tipoMovimentacao(TipoMovimentacao::ENTRADA)
            ->avg('vl_unitario') ?? 0;

        $consumoTotal = round((float) $aplicacoes->sum('nu_quantidade_aplicada'), 2);

        return [
            'periodoDias' => $dias,
            'inicio' => $inicio,
            'fim' => $fim,
            'totalAplicacoes' => $aplicacoes->count(),
            'consumoTotal' => $consumoTotal,
            'areaTratada' => round((float) $aplicacoes->sum('nu_area_aplicada'), 2),
            'custoMedioUnitario' => round((float) $custoMedioUnitario, 2),
            'custoTotal' => round($consumoTotal * $custoMedioUnitario, 2),
            'distribuicaoPorLavoura' => $this->distribuicaoPorLavoura($aplicacoes, $consumoTotal),
            'serieSemanal' => $this->serieSemanal($aplicacoes, $inicio, $fim, $custoMedioUnitario),
        ];
    }

    /**
     * Percentual do consumo total aplicado em cada lavoura (ou "Sem lavoura"
     * quando a aplicação não está vinculada a nenhuma).
     */
    private function distribuicaoPorLavoura(Collection $aplicacoes, float $consumoTotal): Collection
    {
        return $aplicacoes
            ->groupBy(fn (InsumoAplicacao $a) => $a->lavoura?->ds_cultura ?? 'Sem lavoura')
            ->map(function (Collection $doGrupo, string $nome) use ($consumoTotal) {
                $quantidade = round((float) $doGrupo->sum('nu_quantidade_aplicada'), 2);

                return [
                    'nome' => $nome,
                    'quantidade' => $quantidade,
                    'percentual' => $consumoTotal > 0 ? round($quantidade / $consumoTotal * 100, 1) : 0,
                ];
            })
            ->sortByDesc('quantidade')
            ->values();
    }

    /**
     * Quebra o período em semanas (a partir do início) com aplicações,
     * quantidade e custo estimado de cada uma — para a tabela de análise.
     */
    private function serieSemanal(Collection $aplicacoes, Carbon $inicio, Carbon $fim, float $custoMedioUnitario): Collection
    {
        $semanas = collect();
        $inicioSemana = $inicio->copy();
        $numero = 1;

        while ($inicioSemana->lte($fim)) {
            $fimSemana = $inicioSemana->copy()->addDays(6)->min($fim);

            $doPeriodo = $aplicacoes->filter(
                fn (InsumoAplicacao $a) => Carbon::parse($a->dt_aplicacao)->between($inicioSemana, $fimSemana)
            );

            $quantidade = round((float) $doPeriodo->sum('nu_quantidade_aplicada'), 2);

            $semanas->push([
                'label' => "Semana {$numero}",
                'aplicacoes' => $doPeriodo->count(),
                'quantidade' => $quantidade,
                'area' => round((float) $doPeriodo->sum('nu_area_aplicada'), 2),
                'custo' => round($quantidade * $custoMedioUnitario, 2),
            ]);

            $inicioSemana->addDays(7);
            $numero++;
        }

        return $semanas;
    }
}
