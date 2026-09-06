@php
use App\Entity\UsuarioEntity;
@endphp

@extends('layouts.app')

@section('title', 'Dashboard - AgroTwin')

@section('content')
    <div class="dashboard-container mx-auto max-w-[1400px]">
        <!-- Header da propriedade selecionada -->
        <div class="mb-8 flex items-center justify-between gap-4 rounded-xl surface p-5 shadow-sm max-md:flex-col max-md:items-start">
            <div class="property-selector">
                <h1 class="mb-2.5 flex items-center gap-2.5 text-2xl font-semibold text-heading">
                    <i class="fas fa-map-marked-alt text-green-600"></i>
                    @if($selectedPropriedade)
                        {{ $selectedPropriedade->ds_nome }}@if(!empty($selectedPropriedade->ds_localizacao)) -
                        {{ $selectedPropriedade->ds_localizacao }}@endif
                    @else
                        Selecione uma propriedade
                    @endif
                    @if($selectedLavoura)
                        <i class="fas fa-chevron-right text-sm text-muted"></i>
                        <span class="text-lg font-medium text-muted">{{ $selectedLavoura->ds_cultura }}</span>
                    @endif
                </h1>
                <div class="flex flex-wrap gap-3">
                    <select id="propertySelector" class="min-w-[260px] rounded-lg border-2 border-subtle surface px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 transition-all hover:border-blue-500 focus:border-blue-500 focus:outline-none focus:ring-[3px] focus:ring-blue-500/10 max-md:min-w-full">
                        @foreach($propriedades as $propriedade)
                            <option value="{{ $propriedade->id_propriedade }}"
                                {{ (string)$propriedade->id_propriedade === (string)$getPropriedadeById ? 'selected' : '' }}>
                                {{ $propriedade->ds_nome }}@if(!empty($propriedade->ds_localizacao)) - {{ $propriedade->ds_localizacao }}@endif
                            </option>
                        @endforeach
                    </select>

                    @if($lavouras->isNotEmpty())
                        <select id="lavouraSelector" class="min-w-[220px] rounded-lg border-2 border-subtle surface px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 transition-all hover:border-blue-500 focus:border-blue-500 focus:outline-none focus:ring-[3px] focus:ring-blue-500/10 max-md:min-w-full">
                            @foreach($lavouras as $lavoura)
                                <option value="{{ $lavoura->id_lavoura }}"
                                    {{ $selectedLavoura && (string)$lavoura->id_lavoura === (string)$selectedLavoura->id_lavoura ? 'selected' : '' }}>
                                    {{ $lavoura->ds_cultura }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>
            <div class="last-update flex items-center gap-2 text-sm text-muted">
                <span class="status-indicator active text-xs text-green-600">●</span>
                <span>Última atualização: <strong>{{ $dadosDashboard['ultimasLeituras']['ultima_atualizacao'] ?? 'Sem leituras ainda' }}</strong></span>
            </div>
        </div>

        <!-- Status geral -->
        <div class="mb-8">
            @if($dadosDashboard['ultimasLeituras']['status_geral'] === 'sem_sensores')
                <div class="status-card flex items-center gap-3 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 p-4 px-5 font-medium text-gray-700 shadow-sm dark:from-gray-800 dark:to-gray-800 dark:text-gray-300">
                    <i class="fas fa-info-circle text-xl"></i>
                    <span>
                        @if($selectedLavoura)
                            Nenhum sensor cadastrado na lavoura "{{ $selectedLavoura->ds_cultura }}" ainda.
                        @elseif($lavouras->isEmpty())
                            Nenhuma lavoura cadastrada nesta propriedade ainda.
                        @else
                            Nenhum sensor cadastrado nesta propriedade ainda.
                        @endif
                    </span>
                </div>
            @else
                <div class="status-card healthy flex items-center gap-3 rounded-xl bg-gradient-to-br from-green-100 to-green-200 p-4 px-5 font-medium text-green-800 dark:text-green-300 shadow-sm dark:from-green-900/40 dark:to-green-800/40">
                    <i class="fas fa-check-circle text-xl"></i>
                    <span>Monitoramento ativo</span>
                </div>
            @endif
        </div>

        <!-- Indicadores principais -->
        @php
            $indicadores = [
                ['chave' => 'umidade', 'label' => 'Umidade do Solo', 'unidade' => '%', 'icone' => 'fa-tint', 'cor' => 'text-blue-500'],
                ['chave' => 'ph', 'label' => 'pH do Solo', 'unidade' => '', 'icone' => 'fa-flask', 'cor' => 'text-violet-500'],
                ['chave' => 'temperatura', 'label' => 'Temperatura', 'unidade' => '°C', 'icone' => 'fa-thermometer-half', 'cor' => 'text-amber-500'],
                ['chave' => 'condutividade', 'label' => 'Condutividade (EC)', 'unidade' => 'µS/cm', 'icone' => 'fa-bolt', 'cor' => 'text-yellow-500'],
                ['chave' => 'nitrogenio', 'label' => 'Nitrogênio', 'unidade' => 'ppm', 'icone' => 'fa-seedling', 'cor' => 'text-green-500'],
                ['chave' => 'fosforo', 'label' => 'Fósforo', 'unidade' => 'ppm', 'icone' => 'fa-seedling', 'cor' => 'text-green-500'],
                ['chave' => 'potassio', 'label' => 'Potássio', 'unidade' => 'ppm', 'icone' => 'fa-seedling', 'cor' => 'text-green-500'],
                ['chave' => 'npk', 'label' => 'NPK', 'unidade' => '', 'icone' => 'fa-seedling', 'cor' => 'text-green-500'],
            ];
        @endphp
        <div class="mb-8 grid grid-cols-[repeat(auto-fit,minmax(250px,1fr))] gap-5">
            @foreach($indicadores as $indicador)
                @php $valor = $dadosDashboard['sensores'][$indicador['chave']] ?? null; @endphp
                <div class="indicator-card rounded-xl surface p-5 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-lg">
                    <div class="mb-4 flex items-center gap-2.5">
                        <i class="fas {{ $indicador['icone'] }} {{ $indicador['cor'] }}"></i>
                        <span class="text-sm font-medium text-muted">{{ $indicador['label'] }}</span>
                    </div>
                    <div class="mb-2.5 flex items-baseline gap-1">
                        <span class="value text-3xl font-bold text-heading">{{ $valor ?? '—' }}</span>
                        <span class="unit text-lg font-medium text-muted">{{ $valor !== null ? $indicador['unidade'] : '' }}</span>
                    </div>
                    @if($valor !== null)
                        <div class="flex w-fit items-center gap-1.5 rounded-full bg-green-100 dark:bg-green-900/40 px-2 py-1 text-xs font-medium text-green-800 dark:text-green-300">
                            <i class="fas fa-check"></i>
                            <span>Monitorado</span>
                        </div>
                    @else
                        <div class="flex w-fit items-center gap-1.5 rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                            <i class="fas fa-minus"></i>
                            <span>Sem leitura</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Área de gráficos e recomendações -->
        <div class="grid grid-cols-[2fr_1fr] gap-8 max-lg:grid-cols-1">
            <!-- Gráficos -->
            <div class="flex flex-col gap-5">
                <div class="rounded-xl surface p-5 shadow-sm">
                    <div class="mb-5 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-heading">Umidade do Solo - Últimos 7 dias</h3>
                        <select class="chart-period rounded-md border border-gray-300 dark:border-gray-600 surface px-3 py-1.5 text-sm">
                            <option value="24h">24h</option>
                            <option value="7d" selected>7 dias</option>
                            <option value="30d">30 dias</option>
                        </select>
                    </div>
                    <div class="relative h-[250px]">
                        <canvas
                            id="moistureChart"
                            data-labels="{{ json_encode($dadosDashboard['seriesTemporais']['umidade']['labels']) }}"
                            data-valores="{{ json_encode($dadosDashboard['seriesTemporais']['umidade']['valores']) }}"
                        ></canvas>
                    </div>
                </div>

                <div class="rounded-xl surface p-5 shadow-sm">
                    <div class="mb-5 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-heading">pH do Solo - Últimos 7 dias</h3>
                        <select class="chart-period rounded-md border border-gray-300 dark:border-gray-600 surface px-3 py-1.5 text-sm">
                            <option value="24h">24h</option>
                            <option value="7d" selected>7 dias</option>
                            <option value="30d">30 dias</option>
                        </select>
                    </div>
                    <div class="relative h-[250px]">
                        <canvas
                            id="phChart"
                            data-labels="{{ json_encode($dadosDashboard['seriesTemporais']['ph']['labels']) }}"
                            data-valores="{{ json_encode($dadosDashboard['seriesTemporais']['ph']['valores']) }}"
                        ></canvas>
                    </div>
                </div>
            </div>

            <!-- Recomendações e Alertas -->
            <div class="flex flex-col gap-5">
                <!-- Recomendação da IA -->
                <div id="recomendacoes" class="overflow-hidden rounded-xl surface shadow-sm">
                    <div class="flex items-center gap-2.5 bg-gradient-to-br from-indigo-500 to-violet-500 px-5 py-4 text-white">
                        <i class="fas fa-brain"></i>
                        <h3 class="text-base font-semibold">Recomendações</h3>
                    </div>
                    <div class="p-5">
                        @forelse($dadosDashboard['recomendacoes'] as $recomendacao)
                            <p class="mb-3 flex items-start gap-2 leading-relaxed text-gray-700 last:mb-0 dark:text-gray-300">
                                <i class="fas fa-arrow-right mt-1 text-xs text-indigo-500"></i>
                                <span>{{ $recomendacao }}</span>
                            </p>
                        @empty
                            <p class="text-sm text-muted">Nenhuma recomendação no momento — configure os limites das suas lavouras para receber sugestões automáticas.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Alertas Ativos -->
                <div class="overflow-hidden rounded-xl surface shadow-sm">
                    <div class="flex items-center gap-2.5 border-b border-subtle bg-gray-50 dark:bg-gray-900/50 px-5 py-4">
                        <i class="fas fa-bell text-gray-700 dark:text-gray-300"></i>
                        <h3 class="flex-1 text-base font-semibold text-heading">Alertas Ativos</h3>
                        <span class="min-w-[20px] rounded-full bg-red-500 px-2 py-0.5 text-center text-xs font-semibold text-white">{{ count($dadosDashboard['alertas']) }}</span>
                    </div>
                    <div class="px-5">
                        @forelse($dadosDashboard['alertas'] as $alerta)
                            @php
                                $corSeveridade = match($alerta->tp_severidade) {
                                    'critical' => ['bg' => 'bg-red-100 dark:bg-red-900/40', 'text' => 'text-red-600 dark:text-red-400', 'badgeText' => 'text-red-800 dark:text-red-300', 'label' => 'Crítico'],
                                    'info' => ['bg' => 'bg-blue-100 dark:bg-blue-900/40', 'text' => 'text-blue-600 dark:text-blue-400', 'badgeText' => 'text-blue-800 dark:text-blue-300', 'label' => 'Info'],
                                    default => ['bg' => 'bg-amber-100 dark:bg-amber-900/40', 'text' => 'text-amber-600 dark:text-amber-400', 'badgeText' => 'text-amber-800 dark:text-amber-300', 'label' => 'Atenção'],
                                };
                            @endphp
                            <a href="{{ route('alertas.marcarLido', $alerta->id_alerta) }}" class="alert-item flex items-center border-b border-subtle py-4 last:border-b-0 hover-surface">
                                <div class="mr-3 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full {{ $corSeveridade['bg'] }} {{ $corSeveridade['text'] }}">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                                <div class="flex flex-1 flex-col gap-0.5">
                                    <span class="alert-title text-sm font-medium text-heading">{{ $alerta->ds_mensagem }}</span>
                                    <span class="text-xs text-muted">{{ $alerta->dt_alerta->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="rounded-xl {{ $corSeveridade['bg'] }} px-2 py-1 text-[11px] font-semibold uppercase {{ $corSeveridade['badgeText'] }}">
                                    <span>{{ $corSeveridade['label'] }}</span>
                                </div>
                            </a>
                        @empty
                            <p class="py-4 text-sm text-muted">Nenhum alerta ativo no momento.</p>
                        @endforelse
                    </div>
                    <a href="{{ route('alertas.index') }}" class="view-all-alerts btn btn-outline mx-5 mb-5 mt-4">Ver Todos os Alertas</a>
                </div>

                <!-- Sensores Conectados -->
                <div class="overflow-hidden rounded-xl surface shadow-sm">
                    <div class="flex items-center gap-2.5 border-b border-subtle bg-gray-50 dark:bg-gray-900/50 px-5 py-4">
                        <i class="fas fa-satellite-dish text-gray-700 dark:text-gray-300"></i>
                        <h3 class="text-base font-semibold text-heading">Status dos Sensores</h3>
                    </div>
                    <div class="grid grid-cols-2 gap-3 p-5">
                        @forelse($dadosDashboard['sensoresStatus'] as $sensorStatus)
                            @php $online = $sensorStatus->ds_status?->value === 'ativo'; @endphp
                            <div class="flex items-center justify-between rounded-lg border border-subtle p-3 transition-colors hover-surface">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $sensorStatus->ds_nome }}</span>
                                <span class="rounded-xl {{ $online ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' }} px-2 py-1 text-xs font-semibold uppercase">{{ $online ? 'Online' : 'Offline' }}</span>
                            </div>
                        @empty
                            <p class="col-span-2 text-sm text-muted">
                                @if($selectedLavoura)
                                    Nenhum sensor cadastrado na lavoura "{{ $selectedLavoura->ds_cultura }}".
                                @else
                                    Nenhum sensor cadastrado nesta propriedade.
                                @endif
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- dashboard.js já inicializa os gráficos e o seletor de propriedade sozinho (DOMContentLoaded) --}}
    @vite(['resources/js/dashboard/dashboard.js'])
@endpush
