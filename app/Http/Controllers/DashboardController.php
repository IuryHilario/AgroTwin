<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Models\Alerta;
use App\Models\ConfiguracaoLimite;
use App\Models\Insumo;
use App\Services\SensorReadingService;
use App\Services\RecomendacaoService;
use App\Services\ClimaService;
use App\Traits\SelecionaPropriedadeLavoura;

class DashboardController extends Controller
{
    use SelecionaPropriedadeLavoura;

    /**
     * Parâmetros exibidos no dashboard, na ordem de exibição. `tipo` é o
     * TipoSensor — mesma chave das leituras e dos limites configurados por
     * lavoura (tela Configurar Limites).
     */
    private const PARAMETROS = [
        ['tipo' => 'umidade_solo', 'label' => 'Umidade do Solo', 'unidade' => '%', 'icone' => 'fa-droplet'],
        ['tipo' => 'ph', 'label' => 'pH do Solo', 'unidade' => '', 'icone' => 'fa-flask'],
        ['tipo' => 'temperatura', 'label' => 'Temperatura', 'unidade' => '°C', 'icone' => 'fa-temperature-half'],
        ['tipo' => 'condutividade', 'label' => 'Condutividade', 'unidade' => 'µS/cm', 'icone' => 'fa-bolt'],
        ['tipo' => 'nitrogenio', 'label' => 'Nitrogênio (N)', 'unidade' => 'ppm', 'icone' => 'fa-leaf'],
        ['tipo' => 'fosforo', 'label' => 'Fósforo (P)', 'unidade' => 'ppm', 'icone' => 'fa-leaf'],
        ['tipo' => 'potassio', 'label' => 'Potássio (K)', 'unidade' => 'ppm', 'icone' => 'fa-leaf'],
    ];

    public function __construct(
        private SensorReadingService $leituraService,
        private RecomendacaoService $recomendacaoService,
        private ClimaService $climaService
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
        $ultimas = $this->ultimaLeituraPorTipo($sensores);
        $indicadores = $this->montarIndicadores($ultimas, $lavoura);

        return [
            'alertas' => $this->getAlertasAtivos($sensores),
            'recomendacoes' => $lavoura ? $this->recomendacaoService->gerarParaLavoura($lavoura) : [],
            'ultimaLeitura' => collect($ultimas)->max('dt_leitura')?->format('H:i'),
            'sensoresStatus' => $sensores->load('ultimaLeitura'),
            'insumosEmAtencao' => $this->insumosEmAtencao(),
            'seriesTemporais' => $this->getSeriesTemporais($sensores),
            'indicadores' => $indicadores,
            'resumo' => $this->resumirIndicadores($indicadores),
            'irrigacaoAtiva' => (bool) $lavoura?->fl_irrigacao_ativa,
            'clima' => $this->climaService->atual($propriedade->ds_localizacao),
        ];
    }

    /**
     * Insumos que pedem uma providência: vencidos, vencendo nos próximos 30
     * dias ou com estoque abaixo do mínimo. Os dados já estavam no banco e
     * ninguém era avisado.
     */
    private function insumosEmAtencao(): Collection
    {
        return Insumo::where('id_usuario', Auth::id())
            ->get()
            ->map(function (Insumo $insumo) {
                $situacao = match (true) {
                    $insumo->vencido() => ['erro', 'Vencido em ' . $insumo->dt_validade->format('d/m/Y')],
                    $insumo->estoque_abaixo_minimo => ['alerta', 'Estoque abaixo do mínimo'],
                    $insumo->venceEmBreve() => ['alerta', 'Vence em ' . $insumo->dt_validade->format('d/m/Y')],
                    default => null,
                };

                return $situacao ? ['insumo' => $insumo, 'tom' => $situacao[0], 'texto' => $situacao[1]] : null;
            })
            ->filter()
            ->sortBy(fn (array $item) => $item['tom'] === 'erro' ? 0 : 1)
            ->take(5)
            ->values();
    }

    /**
     * Junta a última leitura de cada parâmetro com o limite configurado para a
     * lavoura, para o dashboard mostrar o valor já julgado (dentro/fora da
     * faixa) em vez de um número solto.
     */
    private function montarIndicadores(array $ultimas, $lavoura): Collection
    {
        $limites = $lavoura ? ConfiguracaoLimite::porLavoura($lavoura->id_lavoura) : collect();

        return collect(self::PARAMETROS)->map(function (array $parametro) use ($ultimas, $limites) {
            $valor = $ultimas[$parametro['tipo']]->valor ?? null;
            $limite = $limites[$parametro['tipo']] ?? null;

            return $parametro + [
                'valor' => $valor,
                'min' => $limite?->valor_min,
                'max' => $limite?->valor_max,
                'status' => $this->statusDoParametro($valor, $limite),
                'posicao' => $this->posicaoNaFaixa($valor, $limite),
            ];
        });
    }

    /**
     * ok = dentro da faixa · fora = estourou um dos limites ·
     * sem_limite = tem leitura mas ninguém configurou a faixa ainda ·
     * sem_leitura = nenhum sensor desse tipo reportou.
     */
    private function statusDoParametro(?float $valor, ?ConfiguracaoLimite $limite): string
    {
        if ($valor === null) {
            return 'sem_leitura';
        }

        if (!$limite || ($limite->valor_min === null && $limite->valor_max === null)) {
            return 'sem_limite';
        }

        $abaixo = $limite->valor_min !== null && $valor < $limite->valor_min;
        $acima = $limite->valor_max !== null && $valor > $limite->valor_max;

        return ($abaixo || $acima) ? 'fora' : 'ok';
    }

    /**
     * Posição do valor (0-100%) na régua do medidor. A régua vai um pouco além
     * dos limites (20% da faixa para cada lado) para que um valor fora da faixa
     * ainda apareça dentro da barra, e não grudado na ponta.
     * Só faz sentido quando a lavoura tem mínimo E máximo configurados.
     */
    private function posicaoNaFaixa(?float $valor, ?ConfiguracaoLimite $limite): ?float
    {
        if ($valor === null || !$limite || $limite->valor_min === null || $limite->valor_max === null) {
            return null;
        }

        $faixa = $limite->valor_max - $limite->valor_min;

        if ($faixa <= 0) {
            return null;
        }

        $escalaInicio = $limite->valor_min - ($faixa * 0.2);
        $posicao = (($valor - $escalaInicio) / ($faixa * 1.4)) * 100;

        return round(max(0, min(100, $posicao)), 1);
    }

    private function resumirIndicadores(Collection $indicadores): array
    {
        $fora = $indicadores->where('status', 'fora')->count();
        $ok = $indicadores->where('status', 'ok')->count();

        return [
            'fora' => $fora,
            'ok' => $ok,
            // Só conta como avaliado o parâmetro que tem leitura E faixa configurada —
            // sem os dois não dá para dizer se está bom ou ruim.
            'avaliados' => $ok + $fora,
            'semLimite' => $indicadores->where('status', 'sem_limite')->count(),
            'comLeitura' => $indicadores->whereNotNull('valor')->count(),
        ];
    }

    /**
     * Série diária (últimos 7 dias) de umidade e pH, para os gráficos do
     * dashboard. Usa o primeiro sensor de cada tipo encontrado entre os
     * sensores exibidos (mesmo critério de ultimaLeituraPorTipo).
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
     * Leitura mais recente de cada tipo de sensor (umidade, pH, N, P, K...),
     * indexada pelo tipo. Se a lavoura tiver dois sensores do mesmo tipo,
     * vale o que reportou por último.
     *
     * @return array<string, \App\Models\LeituraSensor>
     */
    private function ultimaLeituraPorTipo($sensores): array
    {
        $ultimas = [];

        foreach ($sensores as $sensor) {
            $leitura = $sensor->tp_sensor ? $this->leituraService->ultimaLeitura($sensor) : null;
            $tipo = $sensor->tp_sensor?->value;

            if ($leitura && (!isset($ultimas[$tipo]) || $leitura->dt_leitura->gt($ultimas[$tipo]->dt_leitura))) {
                $ultimas[$tipo] = $leitura;
            }
        }

        return $ultimas;
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
}
