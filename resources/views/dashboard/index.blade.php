@extends('layouts.app')

@section('title', 'Dashboard - AgroTwin')

@php
    $resumo = $dadosDashboard['resumo'];
    $temSensores = $dadosDashboard['sensoresStatus']->isNotEmpty();

    // Complemento sobre os parâmetros que ficaram sem faixa — sem isso o painel
    // daria a entender que avaliou tudo, quando na verdade avaliou só uma parte.
    $ressalva = $resumo['semLimite'] > 0
        ? ' ' . $resumo['semLimite'] . ' ' . ($resumo['semLimite'] === 1 ? 'parâmetro segue sem faixa definida.' : 'parâmetros seguem sem faixa definida.')
        : '';

    // Veredito do painel: a primeira coisa que o agricultor precisa saber ao abrir a tela.
    $veredito = match (true) {
        !$temSensores => ['tom' => 'neutro', 'icone' => 'fa-satellite-dish', 'titulo' => 'Nenhum sensor nesta seleção', 'detalhe' => 'Cadastre um sensor para começar o monitoramento.'],
        $resumo['comLeitura'] === 0 => ['tom' => 'neutro', 'icone' => 'fa-hourglass-half', 'titulo' => 'Aguardando a primeira leitura', 'detalhe' => 'Os sensores estão cadastrados, mas ainda não enviaram dados.'],
        $resumo['fora'] > 0 => ['tom' => 'alerta', 'icone' => 'fa-triangle-exclamation', 'titulo' => $resumo['fora'] . ' ' . ($resumo['fora'] === 1 ? 'parâmetro fora da faixa' : 'parâmetros fora da faixa'), 'detalhe' => 'Veja abaixo qual precisa de atenção.' . $ressalva],
        $resumo['avaliados'] === 0 => ['tom' => 'neutro', 'icone' => 'fa-sliders', 'titulo' => 'Faixas ideais não configuradas', 'detalhe' => 'Defina os limites da lavoura para o sistema avaliar as leituras.'],
        default => ['tom' => 'ok', 'icone' => 'fa-circle-check', 'titulo' => 'Tudo dentro da faixa', 'detalhe' => ($resumo['avaliados'] === 1 ? 'O parâmetro avaliado está' : 'Os ' . $resumo['avaliados'] . ' parâmetros avaliados estão') . ' no nível ideal.' . $ressalva],
    };

    $tomVeredito = match ($veredito['tom']) {
        'ok' => ['texto' => 'text-emerald-300', 'anel' => 'bg-emerald-400/15 text-emerald-300 ring-emerald-400/30'],
        'alerta' => ['texto' => 'text-amber-300', 'anel' => 'bg-amber-400/15 text-amber-300 ring-amber-400/30'],
        default => ['texto' => 'text-slate-300', 'anel' => 'bg-white/10 text-slate-300 ring-white/20'],
    };
@endphp

@section('content')
    <div class="mx-auto max-w-[1400px]">

        {{-- ==================== PAINEL DA ESTAÇÃO ==================== --}}
        <section class="painel-estacao mb-6 p-6 shadow-lg md:p-8">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="font-readout text-xs uppercase tracking-[0.2em] text-emerald-400/80">Estação de monitoramento</p>
                    <h1 class="mt-1 flex flex-wrap items-center gap-x-2.5 gap-y-1 text-2xl font-semibold md:text-3xl">
                        @if($selectedPropriedade)
                            <span>{{ $selectedPropriedade->ds_nome }}</span>
                            @if($selectedLavoura)
                                <i class="fas fa-chevron-right text-xs text-white/30"></i>
                                <span class="font-normal text-white/70">{{ $selectedLavoura->ds_cultura }}</span>
                            @endif
                        @else
                            <span>Selecione uma propriedade</span>
                        @endif
                    </h1>
                    @if($selectedPropriedade && !empty($selectedPropriedade->ds_localizacao))
                        <p class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-white/50">
                            <span><i class="fas fa-location-dot mr-1"></i>{{ $selectedPropriedade->ds_localizacao }}</span>
                            @if($clima = $dadosDashboard['clima'])
                                <span class="text-white/20">|</span>
                                <span class="text-white/75" title="Tempo atual">
                                    <i class="fas {{ $clima['icone'] }} mr-1 text-amber-300/80"></i>
                                    <span class="font-readout">{{ $clima['temperatura'] }}°C</span>
                                    · {{ $clima['descricao'] }}
                                </span>
                                @php $hoje = $clima['dias'][0] ?? null; @endphp
                                @if($hoje && $hoje['chance_chuva'] !== null)
                                    <span class="font-readout text-xs text-sky-300" title="Chance máxima de chuva hoje{{ $hoje['chuva_mm'] ? ' · ' . number_format($hoje['chuva_mm'], 1, ',', '.') . ' mm previstos' : '' }}">
                                        <i class="fas fa-droplet mr-0.5"></i>chuva hoje {{ $hoje['chance_chuva'] }}%
                                    </span>
                                @endif
                                <span class="font-readout text-xs">
                                    ar {{ $clima['umidade'] }}% · vento {{ $clima['vento_kmh'] }} km/h
                                </span>
                            @endif
                        </p>
                    @endif
                </div>

                <div class="flex flex-wrap gap-2">
                    <select id="propertySelector" aria-label="Selecionar propriedade"
                        class="font-readout min-w-[200px] rounded-lg border border-white/15 bg-white/10 px-3 py-2 text-sm text-white backdrop-blur-sm transition hover:bg-white/15 focus:border-emerald-400/50 focus:outline-none">
                        @foreach($propriedades as $propriedade)
                            <option class="bg-gray-900 text-white" value="{{ $propriedade->id_propriedade }}"
                                {{ (string) $propriedade->id_propriedade === (string) $selectedPropriedade->id_propriedade ? 'selected' : '' }}>
                                {{ $propriedade->ds_nome }}
                            </option>
                        @endforeach
                    </select>

                    @if($lavouras->isNotEmpty())
                        <select id="lavouraSelector" aria-label="Selecionar lavoura"
                            class="font-readout min-w-[170px] rounded-lg border border-white/15 bg-white/10 px-3 py-2 text-sm text-white backdrop-blur-sm transition hover:bg-white/15 focus:border-emerald-400/50 focus:outline-none">
                            @foreach($lavouras as $lavoura)
                                <option class="bg-gray-900 text-white" value="{{ $lavoura->id_lavoura }}"
                                    {{ $selectedLavoura && (string) $lavoura->id_lavoura === (string) $selectedLavoura->id_lavoura ? 'selected' : '' }}>
                                    {{ $lavoura->ds_cultura }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>

            {{-- Veredito + irrigação + última leitura --}}
            <div class="mt-7 grid gap-4 border-t border-white/10 pt-6 lg:grid-cols-[1.4fr_1fr_auto]">
                {{-- Veredito --}}
                <div class="flex items-center gap-4">
                    <span class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl ring-1 {{ $tomVeredito['anel'] }}">
                        <i class="fas {{ $veredito['icone'] }} text-xl"></i>
                    </span>
                    <div class="min-w-0">
                        <p class="text-lg font-semibold leading-tight {{ $tomVeredito['texto'] }}">{{ $veredito['titulo'] }}</p>
                        <p class="mt-0.5 text-sm text-white/50">{{ $veredito['detalhe'] }}</p>
                    </div>
                </div>

                {{-- Irrigação --}}
                @if($selectedLavoura)
                    @php $irrigando = $dadosDashboard['irrigacaoAtiva']; @endphp
                    <div class="flex items-center gap-4 lg:border-l lg:border-white/10 lg:pl-6">
                        <span class="relative flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl ring-1 {{ $irrigando ? 'bg-sky-400/15 text-sky-300 ring-sky-400/30' : 'bg-white/10 text-slate-400 ring-white/20' }}">
                            <i class="fas fa-droplet text-xl"></i>
                            @if($irrigando)
                                <span class="absolute -right-0.5 -top-0.5 flex h-3 w-3">
                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-sky-400 opacity-75"></span>
                                    <span class="relative inline-flex h-3 w-3 rounded-full bg-sky-400"></span>
                                </span>
                            @endif
                        </span>
                        <div class="min-w-0">
                            <p class="text-xs uppercase tracking-wider text-white/40">Irrigação</p>
                            <p class="text-base font-semibold {{ $irrigando ? 'text-sky-300' : 'text-white/70' }}">
                                {{ $irrigando ? 'Irrigando agora' : 'Desligada' }}
                            </p>
            {{-- Ligar/desligar a bomba vai por POST (data-action), nunca por link GET. --}}
                            <button type="button"
                                    data-action="{{ $irrigando ? 'parar-irrigacao' : 'irrigar' }}"
                                    data-url="{{ $irrigando ? route('lavouras.irrigacao.parar', $selectedLavoura->id_lavoura) : route('lavouras.irrigacao.iniciar', $selectedLavoura->id_lavoura) }}"
                                    class="mt-1 inline-flex items-center gap-1.5 text-xs font-medium {{ $irrigando ? 'text-rose-300 hover:text-rose-200' : 'text-emerald-300 hover:text-emerald-200' }} hover:underline">
                                <i class="fas {{ $irrigando ? 'fa-stop' : 'fa-play' }} text-[10px]"></i>
                                {{ $irrigando ? 'Parar irrigação' : 'Iniciar manualmente' }}
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Última leitura --}}
                <div class="flex items-center gap-3 lg:flex-col lg:items-end lg:justify-center lg:border-l lg:border-white/10 lg:pl-6 lg:text-right">
                    <p class="text-xs uppercase tracking-wider text-white/40">Última leitura</p>
                    <p class="font-readout text-2xl font-medium text-white">
                        {{ $dadosDashboard['ultimaLeitura'] ?? '--:--' }}
                    </p>
                </div>
            </div>
        </section>

        {{-- ==================== PARÂMETROS DO SOLO ==================== --}}
        <section class="mb-6">
            <div class="mb-3 flex flex-wrap items-baseline justify-between gap-x-3 gap-y-1">
                <h2 class="text-base font-semibold text-heading">Parâmetros do solo</h2>
                @if($selectedLavoura)
                    {{-- data-action: abre no mesmo modal usado na listagem de lavouras. --}}
                    <a href="{{ route('lavouras.limites', $selectedLavoura->id_lavoura) }}"
                       data-action="limites"
                       class="text-sm font-medium text-green-600 hover:underline dark:text-green-400">
                        <i class="fas fa-sliders mr-1 text-xs"></i>Configurar faixas ideais
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach($dadosDashboard['indicadores'] as $indicador)
                    @php
                        $tom = match ($indicador['status']) {
                            'ok' => ['valor' => 'text-emerald-600 dark:text-emerald-400', 'icone' => 'text-emerald-500', 'marcador' => 'bg-emerald-500', 'selo' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300', 'rotulo' => 'Ideal'],
                            'fora' => ['valor' => 'text-rose-600 dark:text-rose-400', 'icone' => 'text-rose-500', 'marcador' => 'bg-rose-500', 'selo' => 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300', 'rotulo' => 'Fora da faixa'],
                            'sem_limite' => ['valor' => 'text-heading', 'icone' => 'text-gray-400', 'marcador' => 'bg-gray-400', 'selo' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300', 'rotulo' => 'Sem faixa'],
                            default => ['valor' => 'text-gray-300 dark:text-gray-600', 'icone' => 'text-gray-300 dark:text-gray-600', 'marcador' => 'bg-gray-300', 'selo' => 'bg-gray-50 text-gray-400 dark:bg-gray-800 dark:text-gray-500', 'rotulo' => 'Sem leitura'],
                        };
                        $destaque = $indicador['status'] === 'fora';
                    @endphp

                    <article class="rounded-xl border {{ $destaque ? 'border-rose-200 dark:border-rose-900/50' : 'border-subtle' }} surface p-4 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                        <header class="mb-3 flex items-start justify-between gap-2">
                            <span class="flex items-center gap-2 text-sm font-medium text-muted">
                                <i class="fas {{ $indicador['icone'] }} {{ $tom['icone'] }}"></i>
                                {{ $indicador['label'] }}
                            </span>
                            <span class="flex-shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide {{ $tom['selo'] }}">
                                {{ $tom['rotulo'] }}
                            </span>
                        </header>

                        <p class="font-readout flex items-baseline gap-1">
                            <span class="text-3xl font-semibold {{ $tom['valor'] }}">{{ $indicador['valor'] ?? '—' }}</span>
                            @if($indicador['valor'] !== null && $indicador['unidade'])
                                <span class="text-sm text-muted">{{ $indicador['unidade'] }}</span>
                            @endif
                        </p>

                        @if($indicador['posicao'] !== null)
                            {{-- Trilho: a faixa ideal ocupa o miolo (14%–86%); as pontas são a margem
                                 fora da faixa, para o marcador nunca sumir na borda. --}}
                            <div class="mt-4">
                                <div class="medidor-trilho">
                                    <div class="medidor-faixa bg-emerald-500/25" style="left: 14.3%; right: 14.3%;"></div>
                                    <div class="medidor-marcador {{ $tom['marcador'] }}" style="left: {{ $indicador['posicao'] }}%;"></div>
                                </div>
                                <div class="font-readout mt-1.5 flex justify-between text-[11px] text-muted">
                                    <span>mín {{ $indicador['min'] }}</span>
                                    <span>máx {{ $indicador['max'] }}</span>
                                </div>
                            </div>
                        @else
                            <div class="mt-4 h-[30px] text-[11px] text-muted">
                                @if($indicador['status'] === 'sem_limite')
                                    <span><i class="fas fa-circle-info mr-1"></i>Defina mín/máx para avaliar</span>
                                @elseif($indicador['status'] === 'sem_leitura')
                                    <span><i class="fas fa-minus mr-1"></i>Nenhum sensor reportou</span>
                                @endif
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>

        {{-- ==================== TENDÊNCIA + LATERAL ==================== --}}
        {{-- minmax(0,...) evita que o canvas do Chart.js (que tem largura intrínseca
             em px) impeça a coluna de encolher e estoure a página na horizontal. --}}
        <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">

            {{-- Gráficos --}}
            <div class="flex min-w-0 flex-col gap-6">
                <section class="rounded-xl border border-subtle surface p-5 shadow-sm">
                    <div class="mb-4 flex items-baseline justify-between gap-2">
                        <h2 class="text-base font-semibold text-heading">Umidade do solo</h2>
                        <span class="font-readout text-xs uppercase tracking-wider text-muted">últimos 7 dias</span>
                    </div>
                    <div class="relative h-[240px]">
                        <canvas
                            id="moistureChart"
                            data-labels="{{ json_encode($dadosDashboard['seriesTemporais']['umidade']['labels']) }}"
                            data-valores="{{ json_encode($dadosDashboard['seriesTemporais']['umidade']['valores']) }}"
                        ></canvas>
                    </div>
                </section>

                <section class="rounded-xl border border-subtle surface p-5 shadow-sm">
                    <div class="mb-4 flex items-baseline justify-between gap-2">
                        <h2 class="text-base font-semibold text-heading">pH do solo</h2>
                        <span class="font-readout text-xs uppercase tracking-wider text-muted">últimos 7 dias</span>
                    </div>
                    <div class="relative h-[240px]">
                        <canvas
                            id="phChart"
                            data-labels="{{ json_encode($dadosDashboard['seriesTemporais']['ph']['labels']) }}"
                            data-valores="{{ json_encode($dadosDashboard['seriesTemporais']['ph']['valores']) }}"
                        ></canvas>
                    </div>
                </section>
            </div>

            {{-- Coluna lateral --}}
            <div class="flex min-w-0 flex-col gap-6">

                {{-- Alertas --}}
                <section class="overflow-hidden rounded-xl border border-subtle surface shadow-sm">
                    <header class="flex items-center gap-2.5 border-b border-subtle px-5 py-3.5">
                        <i class="fas fa-bell text-muted"></i>
                        <h2 class="flex-1 text-base font-semibold text-heading">Alertas ativos</h2>
                        @if(count($dadosDashboard['alertas']) > 0)
                            <span class="font-readout min-w-[22px] rounded-full bg-rose-500 px-2 py-0.5 text-center text-xs font-semibold text-white">{{ count($dadosDashboard['alertas']) }}</span>
                        @endif
                    </header>

                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($dadosDashboard['alertas'] as $alerta)
                            @php
                                $cor = match($alerta->tp_severidade) {
                                    'critical' => ['bg' => 'bg-rose-100 dark:bg-rose-900/40', 'text' => 'text-rose-600 dark:text-rose-400'],
                                    'info' => ['bg' => 'bg-sky-100 dark:bg-sky-900/40', 'text' => 'text-sky-600 dark:text-sky-400'],
                                    default => ['bg' => 'bg-amber-100 dark:bg-amber-900/40', 'text' => 'text-amber-600 dark:text-amber-400'],
                                };
                            @endphp
                            <button type="button"
                                    data-action="marcar-como-lido"
                                    data-url="{{ route('alertas.marcarLido', $alerta->id_alerta) }}"
                                    title="Marcar como lido"
                                    class="flex w-full items-start gap-3 px-5 py-3.5 text-left transition-colors hover-surface">
                                <span class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg {{ $cor['bg'] }} {{ $cor['text'] }}">
                                    <i class="fas fa-exclamation text-xs"></i>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block break-words text-sm leading-snug text-heading">{{ $alerta->ds_mensagem }}</span>
                                    <span class="font-readout mt-0.5 block text-xs text-muted">{{ $alerta->dt_alerta->format('d/m H:i') }}</span>
                                </span>
                            </button>
                        @empty
                            <p class="px-5 py-6 text-center text-sm text-muted">
                                <i class="fas fa-check-circle mb-1.5 block text-lg text-emerald-500"></i>
                                Nenhum alerta ativo
                            </p>
                        @endforelse
                    </div>

                    <a href="{{ route('alertas.index') }}" class="block border-t border-subtle px-5 py-3 text-center text-sm font-medium text-green-600 transition-colors hover-surface dark:text-green-400">
                        Ver todos os alertas
                    </a>
                </section>

                {{-- Recomendações --}}
                <section id="recomendacoes" class="overflow-hidden rounded-xl border border-subtle surface shadow-sm">
                    <header class="flex items-center gap-2.5 border-b border-subtle px-5 py-3.5">
                        <i class="fas fa-lightbulb text-muted"></i>
                        <h2 class="text-base font-semibold text-heading">Recomendações</h2>
                    </header>
                    <div class="px-5 py-4">
                        @forelse($dadosDashboard['recomendacoes'] as $recomendacao)
                            <p class="mb-3 flex items-start gap-2.5 text-sm leading-relaxed text-gray-700 last:mb-0 dark:text-gray-300">
                                <i class="fas fa-arrow-turn-down mt-1 rotate-[-90deg] text-xs text-green-500"></i>
                                <span>{{ $recomendacao }}</span>
                            </p>
                        @empty
                            <p class="text-sm text-muted">Nenhuma recomendação no momento — configure os limites da lavoura para receber sugestões automáticas.</p>
                        @endforelse
                    </div>
                    <a href="{{ route('recomendacoes.index') }}" class="block border-t border-subtle px-5 py-3 text-center text-sm font-medium text-green-600 transition-colors hover-surface dark:text-green-400">
                        Ver todas as recomendações
                    </a>
                </section>

                {{-- Insumos que pedem providência --}}
                @if($dadosDashboard['insumosEmAtencao']->isNotEmpty())
                    <section class="overflow-hidden rounded-xl border border-subtle surface shadow-sm">
                        <header class="flex items-center gap-2.5 border-b border-subtle px-5 py-3.5">
                            <i class="fas fa-flask text-muted"></i>
                            <h2 class="flex-1 text-base font-semibold text-heading">Insumos em atenção</h2>
                            <span class="font-readout min-w-[22px] rounded-full bg-amber-500 px-2 py-0.5 text-center text-xs font-semibold text-white">
                                {{ $dadosDashboard['insumosEmAtencao']->count() }}
                            </span>
                        </header>
                        <div class="px-5 py-2">
                            @foreach($dadosDashboard['insumosEmAtencao'] as $item)
                                <div class="flex items-center justify-between gap-3 border-b border-subtle py-2.5 last:border-b-0">
                                    <span class="min-w-0 truncate text-sm text-gray-700 dark:text-gray-300" title="{{ $item['insumo']->ds_nome }}">
                                        {{ $item['insumo']->ds_nome }}
                                    </span>
                                    <x-ui.selo :tom="$item['tom']" class="flex-shrink-0">{{ $item['texto'] }}</x-ui.selo>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{ route('insumos.index') }}" class="block border-t border-subtle px-5 py-3 text-center text-sm font-medium text-green-600 transition-colors hover-surface dark:text-green-400">
                            Ver todos os insumos
                        </a>
                    </section>
                @endif

                {{-- Sensores --}}
                <section class="overflow-hidden rounded-xl border border-subtle surface shadow-sm">
                    <header class="flex items-center gap-2.5 border-b border-subtle px-5 py-3.5">
                        <i class="fas fa-satellite-dish text-muted"></i>
                        <h2 class="text-base font-semibold text-heading">Sensores</h2>
                    </header>
                    <div class="px-5 py-2">
                        @forelse($dadosDashboard['sensoresStatus'] as $sensorStatus)
                            @php
                                // Pela última leitura recebida, não pelo status cadastrado.
                                $online = $sensorStatus->estaOnline();
                                $quando = $sensorStatus->ultimaLeitura?->dt_leitura;
                            @endphp
                            <div class="flex items-center justify-between gap-2 border-b border-subtle py-2.5 last:border-b-0">
                                <span class="flex min-w-0 items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <span class="h-1.5 w-1.5 flex-shrink-0 rounded-full {{ $online ? 'bg-emerald-500' : 'bg-rose-400' }}"></span>
                                    <span class="truncate" title="{{ $sensorStatus->ds_nome }}">{{ $sensorStatus->ds_nome }}</span>
                                </span>
                                <span class="font-readout flex-shrink-0 text-[11px] {{ $online ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500 dark:text-rose-400' }}"
                                      title="{{ $quando ? 'Última leitura em ' . $quando->format('d/m/Y H:i') : 'Nenhuma leitura recebida' }}">
                                    {{ $quando ? $quando->diffForHumans(['short' => true]) : 'sem leitura' }}
                                </span>
                            </div>
                        @empty
                            <p class="py-4 text-sm text-muted">
                                @if($selectedLavoura)
                                    Nenhum sensor na lavoura "{{ $selectedLavoura->ds_cultura }}".
                                @else
                                    Nenhum sensor nesta propriedade.
                                @endif
                            </p>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- dashboard.js já inicializa os gráficos e o seletor de propriedade sozinho (DOMContentLoaded) --}}
    @vite(['resources/js/dashboard/dashboard.js'])
@endpush
