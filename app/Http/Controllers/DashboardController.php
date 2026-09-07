<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Models\Alerta;
use App\Services\SensorReadingService;
use App\Services\RecomendacaoService;
use App\Traits\SelecionaPropriedadeLavoura;

class DashboardController extends Controller
{
    use SelecionaPropriedadeLavoura;

    public function __construct(
        private SensorReadingService $leituraService,
        private RecomendacaoService $recomendacaoService
    ) {
    }

    /**
     * Exibe o dashboard principal
     */
    public function index(Request $request)
    {
        [$propriedades, $selectedPropriedade, $lavouras, $selectedLavoura] = $this->selecionarPropriedadeELavoura(
            $request,
            Auth::user()
        );

        // Se não há propriedades, redireciona para criar uma
        if (!$selectedPropriedade) {
            return redirect()->route('propriedade.inserir')
                ->with('info', 'Você precisa cadastrar pelo menos uma propriedade para acessar o dashboard.');
        }

        $dadosDashboard = $this->getDadosDashboard($selectedPropriedade, $selectedLavoura);

        return view('dashboard.index', [
            'propriedades' => $propriedades,
            'selectedPropriedade' => $selectedPropriedade,
            'getPropriedadeById' => $selectedPropriedade->id_propriedade,
            'lavouras' => $lavouras,
            'selectedLavoura' => $selectedLavoura,
            'dadosDashboard' => $dadosDashboard
        ]);
    }

    /**
     * Busca dados específicos para o dashboard da lavoura selecionada. Se a
     * propriedade ainda não tem nenhuma lavoura cadastrada, cai de volta para
     * os sensores ligados diretamente à propriedade (sem lavoura atribuída).
     */
    private function getDadosDashboard($propriedade, $lavoura)
    {
        $sensores = $this->sensoresDaSelecao($propriedade, $lavoura);

        return [
            'sensores' => $this->getDadosSensores($sensores),
            'alertas' => $this->getAlertasAtivos($sensores),
            'recomendacoes' => $lavoura ? $this->recomendacaoService->gerarParaLavoura($lavoura) : [],
            'ultimasLeituras' => $this->getUltimasLeituras($sensores),
            'sensoresStatus' => $sensores,
            'seriesTemporais' => $this->getSeriesTemporais($sensores),
        ];
    }

    /**
     * Série diária (últimos 7 dias) de umidade e pH, para os gráficos do
     * dashboard. Usa o primeiro sensor de cada tipo encontrado entre os
     * sensores exibidos (mesmo critério de getDadosSensores).
     */
    private function getSeriesTemporais($sensores): array
    {
        $fim = now()->endOfDay();
        $inicio = $fim->copy()->subDays(6)->startOfDay();

        $relatorio = $this->leituraService->relatorioPorSensores($sensores, $inicio, $fim);

        return [
            'umidade' => $this->serieDoTipo($relatorio, 'umidade_solo'),
            'ph' => $this->serieDoTipo($relatorio, 'ph'),
        ];
    }

    private function serieDoTipo(Collection $relatorio, string $tipo): array
    {
        $item = $relatorio->first(fn (array $item) => $item['sensor']->tp_sensor?->value === $tipo);

        if (!$item) {
            return ['labels' => [], 'valores' => []];
        }

        return [
            'labels' => $item['serie']->keys()->values()->all(),
            'valores' => $item['serie']->values()->all(),
        ];
    }

    /**
     * Última leitura conhecida de cada parâmetro monitorado (umidade, pH, temperatura, NPK),
     * considerando o sensor mais recentemente atualizado de cada tipo na propriedade.
     */
    private function getDadosSensores($sensores)
    {
        $ultimaPorTipo = [];

        foreach ($sensores as $sensor) {
            if (!$sensor->tp_sensor) {
                continue;
            }

            $ultima = $this->leituraService->ultimaLeitura($sensor);
            if (!$ultima) {
                continue;
            }

            $tipo = $sensor->tp_sensor->value;
            if (!isset($ultimaPorTipo[$tipo]) || $ultima->dt_leitura->gt($ultimaPorTipo[$tipo]->dt_leitura)) {
                $ultimaPorTipo[$tipo] = $ultima;
            }
        }

        return [
            'umidade' => $ultimaPorTipo['umidade_solo']->valor ?? null,
            'ph' => $ultimaPorTipo['ph']->valor ?? null,
            'temperatura' => $ultimaPorTipo['temperatura']->valor ?? null,
            'nitrogenio' => $ultimaPorTipo['nitrogenio']->valor ?? null,
            'fosforo' => $ultimaPorTipo['fosforo']->valor ?? null,
            'potassio' => $ultimaPorTipo['potassio']->valor ?? null,
            'condutividade' => $ultimaPorTipo['condutividade']->valor ?? null,
            'npk' => $this->calcularNpk($ultimaPorTipo),
        ];
    }

    /**
     * O sensor físico (7 em 1) reporta N, P e K separadamente, nunca um valor
     * "npk" combinado. Usa a média dos três quando disponíveis; um sensor do
     * tipo 'npk' explícito (ex.: simulação) tem prioridade se existir.
     */
    private function calcularNpk(array $ultimaPorTipo): ?float
    {
        if (isset($ultimaPorTipo['npk'])) {
            return $ultimaPorTipo['npk']->valor;
        }

        $macronutrientes = collect(['nitrogenio', 'fosforo', 'potassio'])
            ->map(fn ($tipo) => $ultimaPorTipo[$tipo]->valor ?? null)
            ->filter(fn ($valor) => $valor !== null);

        if ($macronutrientes->isEmpty()) {
            return null;
        }

        return round($macronutrientes->avg(), 2);
    }

    /**
     * Alertas não lidos dos sensores exibidos (lavoura ou propriedade selecionada), mais recentes primeiro.
     */
    private function getAlertasAtivos($sensores)
    {
        $idsSensores = $sensores->pluck('id_sensor');

        return Alerta::whereIn('id_sensor', $idsSensores)
            ->where('fl_lida', false)
            ->orderByDesc('dt_alerta')
            ->limit(5)
            ->get();
    }

    /**
     * Horário da leitura mais recente entre todos os sensores exibidos.
     */
    private function getUltimasLeituras($sensores)
    {
        $ultimaGeral = null;

        foreach ($sensores as $sensor) {
            $ultima = $this->leituraService->ultimaLeitura($sensor);
            if ($ultima && (!$ultimaGeral || $ultima->dt_leitura->gt($ultimaGeral))) {
                $ultimaGeral = $ultima->dt_leitura;
            }
        }

        return [
            'ultima_atualizacao' => $ultimaGeral?->format('H:i'),
            'status_geral' => $sensores->isEmpty() ? 'sem_sensores' : 'healthy',
        ];
    }
}
