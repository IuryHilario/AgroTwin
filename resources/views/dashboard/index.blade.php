@php
use App\Entity\UsuarioEntity;
@endphp

@extends('layouts.app')

@section('title', 'Dashboard - AgroTwin')

@push('styles')
    @vite(['resources/css/dashboard/dashboard.css'])
@endpush

@section('content')
    <div class="dashboard-container">
        <!-- Header da propriedade selecionada -->
        <div class="dashboard-header">
            <div class="property-selector">
                <h1>
                    <i class="fas fa-map-marked-alt"></i>
                    @if($selectedPropriedade)
                        {{ $selectedPropriedade->ds_nome }}@if(!empty($selectedPropriedade->ds_localizacao)) -
                        {{ $selectedPropriedade->ds_localizacao }}@endif
                    @else
                        Selecione uma propriedade
                    @endif
                </h1>
                <select class="property-select" id="propertySelector">
                    @foreach($propriedades as $propriedade)
                        <option value="{{ $propriedade->id_propriedade }}"
                            {{ (string)$propriedade->id_propriedade === (string)$getPropriedadeById ? 'selected' : '' }}>
                            {{ $propriedade->ds_nome }}@if(!empty($propriedade->ds_localizacao)) - {{ $propriedade->ds_localizacao }}@endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="last-update">
                <span class="status-indicator active">●</span>
                <span>Última atualização: <strong>10:31</strong></span>
            </div>
        </div>

        <!-- Status geral -->
        <div class="general-status">
            <div class="status-card healthy">
                <i class="fas fa-check-circle"></i>
                <span>Solo em condição ideal</span>
            </div>
        </div>

        <!-- Indicadores principais -->
        <div class="indicators-grid">
            <div class="indicator-card">
                <div class="indicator-header">
                    <i class="fas fa-tint icon-moisture"></i>
                    <span class="indicator-title">Umidade do Solo</span>
                </div>
                <div class="indicator-value">
                    <span class="value">41</span>
                    <span class="unit">%</span>
                </div>
                <div class="indicator-status good">
                    <i class="fas fa-arrow-up"></i>
                    <span>Ideal</span>
                </div>
            </div>

            <div class="indicator-card">
                <div class="indicator-header">
                    <i class="fas fa-flask icon-ph"></i>
                    <span class="indicator-title">pH do Solo</span>
                </div>
                <div class="indicator-value">
                    <span class="value">6.2</span>
                    <span class="unit"></span>
                </div>
                <div class="indicator-status warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Atenção</span>
                </div>
            </div>

            <div class="indicator-card">
                <div class="indicator-header">
                    <i class="fas fa-thermometer-half icon-temp"></i>
                    <span class="indicator-title">Temperatura</span>
                </div>
                <div class="indicator-value">
                    <span class="value">24</span>
                    <span class="unit">°C</span>
                </div>
                <div class="indicator-status good">
                    <i class="fas fa-arrow-up"></i>
                    <span>Ótimo</span>
                </div>
            </div>

            <div class="indicator-card">
                <div class="indicator-header">
                    <i class="fas fa-seedling icon-npk"></i>
                    <span class="indicator-title">NPK</span>
                </div>
                <div class="indicator-value">
                    <span class="value">12-5-10</span>
                    <span class="unit"></span>
                </div>
                <div class="indicator-status good">
                    <i class="fas fa-check"></i>
                    <span>Balanceado</span>
                </div>
            </div>
        </div>

        <!-- Área de gráficos e recomendações -->
        <div class="dashboard-grid">
            <!-- Gráficos -->
            <div class="chart-section">
                <div class="chart-container">
                    <div class="chart-header">
                        <h3>Umidade do Solo - Últimos 7 dias</h3>
                        <select class="chart-period">
                            <option value="24h">24h</option>
                            <option value="7d" selected>7 dias</option>
                            <option value="30d">30 dias</option>
                        </select>
                    </div>
                    <canvas id="moistureChart" width="400" height="200"></canvas>
                </div>

                <div class="chart-container">
                    <div class="chart-header">
                        <h3>pH do Solo - Últimos 7 dias</h3>
                        <select class="chart-period">
                            <option value="24h">24h</option>
                            <option value="7d" selected>7 dias</option>
                            <option value="30d">30 dias</option>
                        </select>
                    </div>
                    <canvas id="phChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Recomendações e Alertas -->
            <div class="recommendations-section">
                <!-- Recomendação da IA -->
                <div class="ai-recommendation">
                    <div class="recommendation-header">
                        <i class="fas fa-brain"></i>
                        <h3>Recomendação da IA</h3>
                    </div>
                    <div class="recommendation-content">
                        <div class="recommendation-type">
                            <span class="type-badge correction">Correção do Solo</span>
                        </div>
                        <p class="recommendation-text">
                            Aplicar <strong>2kg/ha de calcário dolomítico</strong> para correção do pH.
                            O solo está ligeiramente ácido (6.2) e pode afetar a absorção de nutrientes.
                        </p>
                        <div class="recommendation-actions">
                            <button class="btn btn-primary">Aplicar Recomendação</button>
                            <button class="btn btn-secondary">Simular Outra Ação</button>
                        </div>
                    </div>
                </div>

                <!-- Alertas Ativos -->
                <div class="alerts-section">
                    <div class="alerts-header">
                        <i class="fas fa-bell"></i>
                        <h3>Alertas Ativos</h3>
                        <span class="alert-count">3</span>
                    </div>
                    <div class="alerts-list">
                        <div class="alert-item critical">
                            <div class="alert-icon">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <div class="alert-content">
                                <span class="alert-title">Sensor 02 Desconectado</span>
                                <span class="alert-time">Há 2 horas</span>
                            </div>
                            <div class="alert-priority critical">
                                <span>Crítico</span>
                            </div>
                        </div>

                        <div class="alert-item warning">
                            <div class="alert-icon">
                                <i class="fas fa-tint"></i>
                            </div>
                            <div class="alert-content">
                                <span class="alert-title">Umidade baixa - Setor Norte</span>
                                <span class="alert-time">Há 4 horas</span>
                            </div>
                            <div class="alert-priority warning">
                                <span>Atenção</span>
                            </div>
                        </div>

                        <div class="alert-item info">
                            <div class="alert-icon">
                                <i class="fas fa-flask"></i>
                            </div>
                            <div class="alert-content">
                                <span class="alert-title">pH fora do ideal</span>
                                <span class="alert-time">Há 6 horas</span>
                            </div>
                            <div class="alert-priority info">
                                <span>Info</span>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-outline view-all-alerts">Ver Todos os Alertas</button>
                </div>

                <!-- Sensores Conectados -->
                <div class="sensors-status">
                    <div class="sensors-header">
                        <i class="fas fa-satellite-dish"></i>
                        <h3>Status dos Sensores</h3>
                    </div>
                    <div class="sensors-grid">
                        <div class="sensor-item online">
                            <span class="sensor-name">Sensor 01</span>
                            <span class="sensor-status">Online</span>
                        </div>
                        <div class="sensor-item offline">
                            <span class="sensor-name">Sensor 02</span>
                            <span class="sensor-status">Offline</span>
                        </div>
                        <div class="sensor-item online">
                            <span class="sensor-name">Sensor 03</span>
                            <span class="sensor-status">Online</span>
                        </div>
                        <div class="sensor-item online">
                            <span class="sensor-name">Sensor 04</span>
                            <span class="sensor-status">Online</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof initializeCharts === 'function') {
                initializeCharts();
            }

            // Funcionalidade do seletor de propriedade
            const propertySelector = document.getElementById('propertySelector');
            if (propertySelector) {
                propertySelector.addEventListener('change', function () {
                    const selectedPropertyId = this.value;
                    if (selectedPropertyId) {
                        window.location.href = `{{ route('dashboard') }}?id_propriedade=${selectedPropertyId}`;
                    }
                });
            }
        });
    </script>
@endpush
