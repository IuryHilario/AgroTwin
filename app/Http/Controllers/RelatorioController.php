<?php

namespace App\Http\Controllers;

use App\Enums\TipoStatusSensor;
use App\Models\Alerta;
use App\Services\SensorReadingService;
use App\Traits\SelecionaPropriedadeLavoura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RelatorioController extends Controller
{
    use SelecionaPropriedadeLavoura;

    private const PERIODOS_VALIDOS = [7, 30, 90];

    public function __construct(private SensorReadingService $leituraService)
    {
    }

    /**
     * Painel de relatórios: evolução dos sensores, volume de leituras e
     * alertas do período, para a propriedade/lavoura selecionada.
     */
    public function index(Request $request)
    {
        [$propriedades, $selectedPropriedade, $lavouras, $selectedLavoura] = $this->selecionarPropriedadeELavoura(
            $request,
            Auth::user()
        );

        if (!$selectedPropriedade) {
            return redirect()->route('propriedade.inserir')
                ->with('info', 'Você precisa cadastrar pelo menos uma propriedade para ver relatórios.');
        }

        $periodoDias = (int) $request->input('periodo', 30);
        if (!in_array($periodoDias, self::PERIODOS_VALIDOS, true)) {
            $periodoDias = 30;
        }

        $fim = now()->endOfDay();
        $inicio = $fim->copy()->subDays($periodoDias - 1)->startOfDay();

        $sensores = $this->sensoresDaSelecao($selectedPropriedade, $selectedLavoura);
        $relatorioSensores = $this->leituraService->relatorioPorSensores($sensores, $inicio, $fim);

        $idsSensores = $sensores->pluck('id_sensor');

        $alertas = Alerta::whereIn('id_sensor', $idsSensores)
            ->whereBetween('dt_alerta', [$inicio, $fim])
            ->orderByDesc('dt_alerta')
            ->get();

        return view('relatorios.index', [
            'propriedades' => $propriedades,
            'selectedPropriedade' => $selectedPropriedade,
            'lavouras' => $lavouras,
            'selectedLavoura' => $selectedLavoura,
            'periodoDias' => $periodoDias,
            'periodosValidos' => self::PERIODOS_VALIDOS,
            'relatorioSensores' => $relatorioSensores,
            'kpis' => [
                'totalLeituras' => $relatorioSensores->sum(fn ($item) => $item['resumo']['quantidade']),
                'sensoresAtivos' => $sensores->where('ds_status', TipoStatusSensor::ATIVO)->count(),
                'totalSensores' => $sensores->count(),
                'totalAlertas' => $alertas->count(),
            ],
            'alertas' => $alertas->take(8),
            'distribuicaoLeituras' => $relatorioSensores
                ->filter(fn ($item) => $item['resumo']['quantidade'] > 0)
                ->map(fn ($item) => [
                    'nome' => $item['sensor']->tp_sensor?->label() ?? $item['sensor']->ds_nome,
                    'quantidade' => $item['resumo']['quantidade'],
                ])
                ->sortByDesc('quantidade')
                ->values(),
        ]);
    }
}
