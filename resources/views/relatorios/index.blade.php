@extends('layouts.app')

@section('title', 'Relatórios - AgroTwin')

@php
    // Aparência de cada situação do diagnóstico, usada nos cards e nas ações.
    $aparencia = [
        'critico' => ['rotulo' => 'Precisa de ação', 'icone' => 'fa-circle-exclamation', 'texto' => 'text-rose-700 dark:text-rose-300', 'fundo' => 'bg-rose-100 dark:bg-rose-900/40', 'barra' => 'bg-rose-500', 'traco' => 'border-l-rose-500', 'borda' => 'border-rose-300 dark:border-rose-800'],
        'atencao' => ['rotulo' => 'Atenção', 'icone' => 'fa-triangle-exclamation', 'texto' => 'text-amber-700 dark:text-amber-300', 'fundo' => 'bg-amber-100 dark:bg-amber-900/40', 'barra' => 'bg-amber-500', 'traco' => 'border-l-amber-500', 'borda' => 'border-amber-300 dark:border-amber-800'],
        'ideal' => ['rotulo' => 'Dentro do ideal', 'icone' => 'fa-circle-check', 'texto' => 'text-emerald-700 dark:text-emerald-300', 'fundo' => 'bg-emerald-100 dark:bg-emerald-900/40', 'barra' => 'bg-emerald-500', 'traco' => 'border-l-emerald-500', 'borda' => 'border-emerald-300 dark:border-emerald-800'],
        'sem_limite' => ['rotulo' => 'Sem faixa ideal', 'icone' => 'fa-sliders', 'texto' => 'text-slate-600 dark:text-slate-300', 'fundo' => 'bg-slate-100 dark:bg-slate-700/60', 'barra' => 'bg-slate-400', 'traco' => 'border-l-slate-300 dark:border-l-slate-600', 'borda' => 'border-subtle'],
        'sem_dados' => ['rotulo' => 'Sem leituras', 'icone' => 'fa-plug-circle-xmark', 'texto' => 'text-slate-600 dark:text-slate-300', 'fundo' => 'bg-slate-100 dark:bg-slate-700/60', 'barra' => 'bg-slate-400', 'traco' => 'border-l-slate-300 dark:border-l-slate-600', 'borda' => 'border-subtle'],
    ];
    $geral = $diagnostico['situacaoGeral'];
    $indice = $diagnostico['indice'];
@endphp

@section('content')
    <div class="relatorios-container mx-auto max-w-[1400px]">

        <!-- Cabeçalho + filtros -->
        <div class="painel-estacao mb-6 p-6 shadow-lg md:p-8">
            <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="font-readout text-xs uppercase tracking-[0.2em] text-emerald-400/80">Relatórios</p>
                    <h1 class="mt-1 text-2xl font-semibold md:text-3xl">Diagnóstico do solo</h1>
                    <p class="mt-1 text-sm text-white/55">
                        {{ $selectedPropriedade->ds_nome }}@if($selectedLavoura) &middot; {{ $selectedLavoura->ds_cultura }}@endif
                        &middot; {{ $inicio->format('d/m') }} a {{ $fim->format('d/m/Y') }}
                    </p>
                </div>
                <a href="{{ route('relatorios.pdf', request()->query()) }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-white/15 bg-white/10 px-4 py-2 text-sm font-medium text-white backdrop-blur-sm transition hover:bg-white/20">
                    <i class="fas fa-file-pdf text-rose-300"></i> Exportar PDF
                </a>
            </div>

            <form method="GET" action="{{ route('relatorios.index') }}" class="grid grid-cols-12 gap-3">
                <div class="col-span-12 md:col-span-4">
                    <select name="id_propriedade" onchange="this.form.submit()"
                        class="w-full rounded-lg border-0 bg-white/95 px-3 py-2 text-sm text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-white/50">
                        @foreach ($propriedades as $propriedade)
                            <option value="{{ $propriedade->id_propriedade }}" @selected($propriedade->id_propriedade === $selectedPropriedade->id_propriedade)>
                                {{ $propriedade->ds_nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-12 md:col-span-4">
                    <select name="id_lavoura" onchange="this.form.submit()"
                        class="w-full rounded-lg border-0 bg-white/95 px-3 py-2 text-sm text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-white/50">
                        <option value="">Todas as lavouras (sensores da propriedade)</option>
                        @foreach ($lavouras as $lavoura)
                            <option value="{{ $lavoura->id_lavoura }}" @selected($selectedLavoura && $lavoura->id_lavoura === $selectedLavoura->id_lavoura)>
                                {{ $lavoura->ds_cultura }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-12 md:col-span-4">
                    <div class="flex gap-1.5 rounded-lg bg-white/15 p-1">
                        @foreach ($periodosValidos as $opcao)
                            <button type="submit" name="periodo" value="{{ $opcao }}"
                                class="flex-1 rounded-md px-3 py-1.5 text-sm font-medium transition-colors {{ $periodoDias === $opcao ? 'bg-white text-green-700' : 'text-white hover:bg-white/10' }}">
                                {{ $opcao }}d
                            </button>
                        @endforeach
                    </div>
                </div>
                <input type="hidden" name="id_propriedade" value="{{ $selectedPropriedade->id_propriedade }}">
                @if($selectedLavoura)
                    <input type="hidden" name="id_lavoura" value="{{ $selectedLavoura->id_lavoura }}">
                @endif
            </form>
        </div>

        <!-- Veredito do período: índice de saúde + números do monitoramento -->
        @php $g = $aparencia[$geral['nivel']] ?? $aparencia['sem_limite']; @endphp
        <div class="mb-6 grid grid-cols-12 gap-4">
            <div class="col-span-12 rounded-xl surface p-5 shadow-sm md:p-6 lg:col-span-7">
                <div class="flex flex-col items-start gap-5 sm:flex-row sm:items-center">
                    @if ($indice !== null)
                        @php
                            $anel = $indice >= 85 ? '#10b981' : ($indice >= 60 ? '#f59e0b' : '#f43f5e');
                        @endphp
                        <div class="relative h-24 w-24 flex-shrink-0 rounded-full"
                             style="background: conic-gradient({{ $anel }} {{ $indice * 3.6 }}deg, rgba(148,163,184,0.25) 0deg)">
                            <div class="absolute inset-[7px] flex flex-col items-center justify-center rounded-full bg-white dark:bg-gray-800">
                                <span class="font-readout text-2xl font-bold text-heading">{{ $indice }}<span class="text-sm">%</span></span>
                                <span class="text-[10px] uppercase tracking-wider text-muted">no ideal</span>
                            </div>
                        </div>
                    @else
                        <div class="flex h-24 w-24 flex-shrink-0 items-center justify-center rounded-full {{ $g['fundo'] }} {{ $g['texto'] }}">
                            <i class="fas {{ $g['icone'] }} text-3xl"></i>
                        </div>
                    @endif

                    <div class="min-w-0">
                        <span class="inline-flex items-center gap-1.5 rounded-full {{ $g['fundo'] }} px-2.5 py-1 text-xs font-semibold {{ $g['texto'] }}">
                            <i class="fas {{ $g['icone'] }}"></i> {{ $geral['titulo'] }}
                        </span>
                        <p class="mt-2 text-sm text-body">{{ $geral['texto'] }}</p>
                        @if ($indice !== null)
                            <p class="mt-1 text-xs text-muted">
                                O índice é a média de quanto tempo cada parâmetro ficou dentro da faixa ideal desta lavoura.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-span-12 grid grid-cols-2 gap-4 lg:col-span-5">
                @foreach ([
                    ['valor' => $kpis['totalLeituras'], 'label' => 'Leituras recebidas', 'icone' => 'fa-database', 'cor' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400'],
                    ['valor' => $kpis['sensoresAtivos'] . '/' . $kpis['totalSensores'], 'label' => 'Sensores ativos', 'icone' => 'fa-satellite-dish', 'cor' => 'bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-400'],
                    ['valor' => $kpis['totalAlertas'], 'label' => 'Alertas no período', 'icone' => 'fa-bell', 'cor' => $kpis['totalAlertas'] > 0 ? 'bg-amber-100 text-amber-600 dark:bg-amber-900/40 dark:text-amber-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400'],
                    ['valor' => $geral['criticos'] + $geral['atencao'], 'label' => 'Parâmetros fora do ideal', 'icone' => 'fa-crosshairs', 'cor' => ($geral['criticos'] + $geral['atencao']) > 0 ? 'bg-rose-100 text-rose-600 dark:bg-rose-900/40 dark:text-rose-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400'],
                ] as $kpi)
                    <div class="rounded-xl surface p-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full {{ $kpi['cor'] }}">
                                <i class="fas {{ $kpi['icone'] }}"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="font-readout text-xl font-bold text-heading">{{ $kpi['valor'] }}</p>
                                <p class="text-xs text-muted">{{ $kpi['label'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-12 gap-6">
            <!-- Parâmetros do solo -->
            <div class="col-span-12 lg:col-span-8">
                <h2 class="mb-3 flex items-center gap-2 font-semibold text-heading">
                    <i class="fas fa-wave-square"></i> Como cada parâmetro se comportou
                </h2>

                @forelse ($diagnostico['parametros'] as $p)
                    @php
                        $a = $aparencia[$p['situacao']];
                        $total = max(1, $p['contagem']['total']);
                        $pctAbaixo = round($p['contagem']['abaixo'] / $total * 100);
                        $pctAcima = round($p['contagem']['acima'] / $total * 100);
                        $pctDentro = 100 - $pctAbaixo - $pctAcima;
                    @endphp
                    <div class="mb-4 rounded-xl border-l-4 {{ $a['traco'] }} surface p-5 shadow-sm">
                        <div class="mb-3 flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-semibold text-heading">{{ $p['rotulo'] }}</h3>
                                    <span class="inline-flex items-center gap-1 rounded-full {{ $a['fundo'] }} px-2 py-0.5 text-[11px] font-semibold {{ $a['texto'] }}">
                                        <i class="fas {{ $a['icone'] }}"></i> {{ $a['rotulo'] }}
                                    </span>
                                    @if ($p['tendencia'] && $p['tendencia']['sentido'] !== 'estavel')
                                        @php $subindo = $p['tendencia']['sentido'] === 'subindo'; @endphp
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-600 dark:bg-slate-700/60 dark:text-slate-300"
                                              title="Comparação entre a primeira e a segunda metade do período">
                                            <i class="fas {{ $subindo ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                                            {{ $subindo ? 'Subindo' : 'Caindo' }} <span class="font-readout">{{ number_format(abs($p['tendencia']['variacao']), 1, ',', '.') }}%</span>
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-muted">{{ $p['sensor']->ds_nome }}</p>
                            </div>
                            <div class="flex gap-2 text-xs">
                                @foreach ([['Mín', 'minimo'], ['Média', 'media'], ['Máx', 'maximo']] as [$rotulo, $campo])
                                    <span class="rounded-lg border border-subtle px-2.5 py-1 text-body">
                                        {{ $rotulo }}
                                        <span class="font-readout font-semibold text-heading">{{ \App\Utils\Util::formatNumber($p['resumo'][$campo], 1) ?? '—' }}</span>{{ $p['unidade'] }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <p class="mb-3 text-sm text-body">{{ $p['diagnostico'] }}</p>

                        @if ($p['faixa'] && $p['contagem']['total'] > 0)
                            <div class="mb-3">
                                <div class="flex h-2.5 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                                    @if ($pctAbaixo > 0)<div class="h-full bg-sky-500" style="width: {{ $pctAbaixo }}%"></div>@endif
                                    @if ($pctDentro > 0)<div class="h-full bg-emerald-500" style="width: {{ $pctDentro }}%"></div>@endif
                                    @if ($pctAcima > 0)<div class="h-full bg-rose-500" style="width: {{ $pctAcima }}%"></div>@endif
                                </div>
                                <div class="mt-1.5 flex flex-wrap gap-x-4 gap-y-1 text-[11px] text-muted">
                                    <span><span class="mr-1 inline-block h-2 w-2 rounded-full bg-sky-500"></span>Abaixo <span class="font-readout">{{ $pctAbaixo }}%</span></span>
                                    <span><span class="mr-1 inline-block h-2 w-2 rounded-full bg-emerald-500"></span>Na faixa ideal <span class="font-readout">{{ $pctDentro }}%</span></span>
                                    <span><span class="mr-1 inline-block h-2 w-2 rounded-full bg-rose-500"></span>Acima <span class="font-readout">{{ $pctAcima }}%</span></span>
                                    <span class="ml-auto">
                                        Faixa ideal:
                                        <span class="font-readout">{{ \App\Utils\Util::formatNumber($p['faixa']['min'], 1) ?? '—' }} a {{ \App\Utils\Util::formatNumber($p['faixa']['max'], 1) ?? '—' }}</span>{{ $p['unidade'] }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        @if ($p['resumo']['quantidade'] > 0)
                            <div class="relative h-[180px]">
                                <canvas
                                    class="sensor-report-chart"
                                    data-labels="{{ $p['serie']->keys()->toJson() }}"
                                    data-valores="{{ $p['serie']->values()->toJson() }}"
                                    data-unidade="{{ $p['unidade'] }}"
                                    @if ($p['faixa'])
                                        data-min="{{ $p['faixa']['min'] }}"
                                        data-max="{{ $p['faixa']['max'] }}"
                                    @endif
                                ></canvas>
                            </div>
                        @endif

                        @if ($p['acao'])
                            <div class="mt-3 flex items-start gap-2.5 rounded-lg border {{ $a['borda'] }} {{ $a['fundo'] }} p-3">
                                <i class="fas fa-lightbulb mt-0.5 {{ $a['texto'] }}"></i>
                                <p class="text-sm text-body">{{ $p['acao'] }}</p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="rounded-xl surface p-8 text-center text-muted shadow-sm">
                        <i class="fas fa-microchip mb-2 block text-2xl"></i>
                        Nenhum sensor cadastrado para esta seleção.
                    </div>
                @endforelse
            </div>

            <!-- Lateral: ações, volume e alertas -->
            <div class="col-span-12 lg:col-span-4">
                <div class="mb-6 rounded-xl surface p-5 shadow-sm">
                    <h2 class="mb-1 flex items-center gap-2 font-semibold text-heading">
                        <i class="fas fa-clipboard-check"></i> O que fazer agora
                    </h2>
                    <p class="mb-4 text-xs text-muted">Sugestões a partir das leituras do período, das mais urgentes para as menos.</p>

                    @forelse ($diagnostico['acoes'] as $indiceAcao => $acao)
                        @php $aa = $aparencia[$acao['situacao']]; @endphp
                        <div class="mb-3 flex items-start gap-3 last:mb-0">
                            <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full {{ $aa['fundo'] }} font-readout text-xs font-bold {{ $aa['texto'] }}">
                                {{ $indiceAcao + 1 }}
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-heading">{{ $acao['rotulo'] }}</p>
                                <p class="text-sm text-body">{{ $acao['texto'] }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="flex items-start gap-3 rounded-lg bg-emerald-50 p-3 dark:bg-emerald-900/20">
                            <i class="fas fa-circle-check mt-0.5 text-emerald-600 dark:text-emerald-400"></i>
                            <p class="text-sm text-body">Nada exige ação no período. Siga o manejo atual e acompanhe os alertas.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mb-6 rounded-xl surface p-5 shadow-sm">
                    <h2 class="mb-4 flex items-center gap-2 font-semibold text-heading">
                        <i class="fas fa-layer-group"></i> Volume de leituras por sensor
                    </h2>
                    @php $corIdx = ['bg-blue-600', 'bg-green-600', 'bg-sky-500', 'bg-amber-500', 'bg-purple-500', 'bg-rose-500', 'bg-teal-500']; @endphp
                    @forelse ($distribuicaoLeituras as $index => $item)
                        @php
                            $maior = $distribuicaoLeituras->max('quantidade') ?: 1;
                            $largura = max(4, round($item['quantidade'] / $maior * 100));
                        @endphp
                        <div class="mb-1.5 flex items-center justify-between text-sm">
                            <span class="text-heading">{{ $item['nome'] }}</span>
                            <span class="font-readout text-muted">{{ $item['quantidade'] }}</span>
                        </div>
                        <div class="mb-3 h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                            <div class="h-full {{ $corIdx[$index % count($corIdx)] }}" style="width: {{ $largura }}%"></div>
                        </div>
                    @empty
                        <p class="text-sm text-muted">Sem leituras no período.</p>
                    @endforelse
                </div>

                <div class="rounded-xl surface p-5 shadow-sm">
                    <h2 class="mb-4 flex items-center gap-2 font-semibold text-heading">
                        <i class="fas fa-bell"></i> Alertas recentes
                    </h2>
                    @forelse ($alertas as $alerta)
                        @php
                            $cor = match($alerta->tp_severidade) {
                                'critical' => ['bg' => 'bg-red-100 dark:bg-red-900/40', 'text' => 'text-red-600 dark:text-red-400', 'label' => 'Crítico'],
                                'info' => ['bg' => 'bg-blue-100 dark:bg-blue-900/40', 'text' => 'text-blue-600 dark:text-blue-400', 'label' => 'Info'],
                                default => ['bg' => 'bg-amber-100 dark:bg-amber-900/40', 'text' => 'text-amber-600 dark:text-amber-400', 'label' => 'Atenção'],
                            };
                        @endphp
                        <div class="mb-3 flex items-start gap-3 border-b border-subtle pb-3 last:mb-0 last:border-b-0 last:pb-0">
                            <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full {{ $cor['bg'] }} {{ $cor['text'] }}">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm text-heading">{{ $alerta->ds_mensagem }}</p>
                                <p class="text-xs text-muted">{{ $alerta->dt_alerta->format('d/m/Y H:i') }} &middot; {{ $cor['label'] }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-muted">Nenhum alerta no período.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/relatorios/index.js'])
@endpush
