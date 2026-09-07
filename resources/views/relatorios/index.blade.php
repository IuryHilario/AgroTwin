@extends('layouts.app')

@section('title', 'Relatórios - AgroTwin')

@section('content')
    <div class="relatorios-container mx-auto max-w-[1400px]">

        <!-- Hero + Filtros -->
        <div class="mb-6 overflow-hidden rounded-xl bg-gradient-to-br from-green-600 to-green-700 p-6 text-white shadow-sm">
            <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="flex items-center gap-2.5 text-2xl font-semibold">
                        <i class="fas fa-chart-line"></i>
                        Relatórios
                    </h1>
                    <p class="mt-1 text-sm text-green-100">
                        {{ $selectedPropriedade->ds_nome }}@if($selectedLavoura) &middot; {{ $selectedLavoura->ds_cultura }}@endif
                        &middot; últimos {{ $periodoDias }} dias
                    </p>
                </div>
                <div class="rounded-lg bg-white/15 px-3 py-1.5 text-xs font-medium backdrop-blur-sm">
                    <i class="fas fa-clock mr-1"></i> Gerado em {{ now()->format('d/m/Y H:i') }}
                </div>
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

        <!-- KPIs -->
        <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-xl surface p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
                        <i class="fas fa-database"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-heading">{{ $kpis['totalLeituras'] }}</p>
                        <p class="text-xs text-muted">Leituras no período</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl surface p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-400">
                        <i class="fas fa-satellite-dish"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-heading">{{ $kpis['sensoresAtivos'] }}/{{ $kpis['totalSensores'] }}</p>
                        <p class="text-xs text-muted">Sensores ativos</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl surface p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full {{ $kpis['totalAlertas'] > 0 ? 'bg-amber-100 text-amber-600 dark:bg-amber-900/40 dark:text-amber-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-heading">{{ $kpis['totalAlertas'] }}</p>
                        <p class="text-xs text-muted">Alertas no período</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl surface p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-sky-100 text-sky-600 dark:bg-sky-900/40 dark:text-sky-400">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-heading">{{ $periodoDias }}</p>
                        <p class="text-xs text-muted">Dias analisados</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-6">
            <!-- Gráficos por sensor -->
            <div class="col-span-12 lg:col-span-8">
                <h6 class="mb-3 flex items-center font-semibold text-heading">
                    <i class="fas fa-wave-square mr-2"></i>
                    Evolução por Sensor
                </h6>

                @forelse ($relatorioSensores as $item)
                    @php
                        $sensor = $item['sensor'];
                        $resumo = $item['resumo'];
                        $unidade = $sensor->tp_sensor ? $sensor->tp_sensor->unidade() : '';
                    @endphp
                    <div class="mb-4 rounded-xl surface p-5 shadow-sm">
                        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <h6 class="font-semibold text-heading">{{ $sensor->ds_nome }}</h6>
                                <p class="text-xs text-muted">{{ $sensor->tp_sensor?->label() ?? 'Sem tipo' }}</p>
                            </div>
                            <div class="flex gap-2 text-xs">
                                <span class="rounded-full bg-sky-100 px-2.5 py-1 font-semibold text-sky-700 dark:bg-sky-900/40 dark:text-sky-300">Mín {{ $resumo['minimo'] ?? '-' }}{{ $unidade }}</span>
                                <span class="rounded-full bg-amber-100 px-2.5 py-1 font-semibold text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">Média {{ $resumo['media'] ?? '-' }}{{ $unidade }}</span>
                                <span class="rounded-full bg-green-100 px-2.5 py-1 font-semibold text-green-700 dark:bg-green-900/40 dark:text-green-300">Máx {{ $resumo['maximo'] ?? '-' }}{{ $unidade }}</span>
                            </div>
                        </div>

                        @if ($resumo['quantidade'] > 0)
                            <div class="relative h-[180px]">
                                <canvas
                                    class="sensor-report-chart"
                                    data-labels="{{ $item['serie']->keys()->toJson() }}"
                                    data-valores="{{ $item['serie']->values()->toJson() }}"
                                    data-unidade="{{ $unidade }}"
                                ></canvas>
                            </div>
                        @else
                            <p class="py-8 text-center text-sm text-muted">Nenhuma leitura registrada neste período.</p>
                        @endif
                    </div>
                @empty
                    <div class="rounded-xl surface p-8 text-center text-muted shadow-sm">
                        <i class="fas fa-microchip mb-2 block text-2xl"></i>
                        Nenhum sensor cadastrado para esta seleção.
                    </div>
                @endforelse
            </div>

            <!-- Lateral: distribuição + alertas -->
            <div class="col-span-12 lg:col-span-4">
                <div class="mb-6 rounded-xl surface p-5 shadow-sm">
                    <h6 class="mb-4 flex items-center font-semibold text-heading">
                        <i class="fas fa-layer-group mr-2"></i>
                        Volume de Leituras por Sensor
                    </h6>
                    @php $corIdx = ['bg-blue-600', 'bg-green-600', 'bg-sky-500', 'bg-amber-500', 'bg-purple-500', 'bg-rose-500', 'bg-teal-500']; @endphp
                    @forelse ($distribuicaoLeituras as $index => $item)
                        @php
                            $maior = $distribuicaoLeituras->max('quantidade') ?: 1;
                            $largura = max(4, round($item['quantidade'] / $maior * 100));
                        @endphp
                        <div class="mb-1.5 flex items-center justify-between text-sm">
                            <span class="text-heading">{{ $item['nome'] }}</span>
                            <span class="text-muted">{{ $item['quantidade'] }}</span>
                        </div>
                        <div class="mb-3 h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                            <div class="h-full {{ $corIdx[$index % count($corIdx)] }}" style="width: {{ $largura }}%"></div>
                        </div>
                    @empty
                        <p class="text-sm text-muted">Sem leituras no período.</p>
                    @endforelse
                </div>

                <div class="rounded-xl surface p-5 shadow-sm">
                    <h6 class="mb-4 flex items-center font-semibold text-heading">
                        <i class="fas fa-bell mr-2"></i>
                        Alertas Recentes
                    </h6>
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
