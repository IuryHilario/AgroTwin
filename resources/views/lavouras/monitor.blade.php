<x-ui.modal-funcional
    modalId="monitorLavoura"
    title="Monitoramento"
    icon="fas fa-desktop"
    bgColor="bg-dark"
    size="modal-xl"
    :item="$lavoura ?? null"
    resourceName="lavoura"
    :additionalButtons="[
        [
            'tag' => 'button',
            'text' => 'Atualizar Dados',
            'class' => 'btn-success',
            'icon' => 'fas fa-sync-alt',
            'onclick' => 'alert(\'Funcionalidade de atualização será implementada\')'
        ],
        [
            'tag' => 'button',
            'text' => 'Gerar Relatório',
            'class' => 'btn-info',
            'icon' => 'fas fa-file-alt',
            'onclick' => 'alert(\'Funcionalidade de relatório será implementada\')'
        ]
    ]"
>
    <!-- Conteúdo específico do monitoramento -->
    @if($lavoura)
        <!-- Resumo da Lavoura -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="fas fa-seedling me-2"></i>
                            Informações da Lavoura
                        </h6>
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Cultura:</strong> {{ $lavoura->ds_cultura ?? 'Não informada' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Status:</strong>
                                <span class="badge bg-success">{{ $lavoura->tp_status ? $lavoura->tp_status->value : 'Ativo' }}</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Plantio:</strong> {{ $lavoura->dt_plantio ? \Carbon\Carbon::parse($lavoura->dt_plantio)->format('d/m/Y') : 'Não informado' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Colheita:</strong> {{ $lavoura->dt_colheita ? \Carbon\Carbon::parse($lavoura->dt_colheita)->format('d/m/Y') : 'Não informado' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Métricas em Tempo Real -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <i class="fas fa-thermometer-half fa-2x text-primary mb-2"></i>
                        <h6>Temperatura</h6>
                        <h4 class="text-primary">24.5°C</h4>
                        <small class="text-muted">Ideal: 20-28°C</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <i class="fas fa-tint fa-2x text-info mb-2"></i>
                        <h6>Umidade Solo</h6>
                        <h4 class="text-info">67%</h4>
                        <small class="text-muted">Ideal: 60-80%</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="fas fa-sun fa-2x text-success mb-2"></i>
                        <h6>Luminosidade</h6>
                        <h4 class="text-success">85%</h4>
                        <small class="text-muted">Ideal: 70-90%</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-flask fa-2x text-warning mb-2"></i>
                        <h6>pH do Solo</h6>
                        <h4 class="text-warning">6.8</h4>
                        <small class="text-muted">Ideal: 6.0-7.0</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Tendências (Simulado) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Tendências das Últimas 24 Horas
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="monitorChart" style="height: 200px; background: #f8f9fa; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                            <div style="text-align: center; color: #6c757d;">
                                <i class="fas fa-chart-area fa-3x mb-2"></i><br>
                                Gráfico de Monitoramento<br>
                                <small>(Implementar com Chart.js)</small>
                            </div>
                        </canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertas e Notificações -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            Status Normal
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li><i class="fas fa-check text-success me-2"></i>Temperatura dentro do ideal</li>
                            <li><i class="fas fa-check text-success me-2"></i>Umidade adequada</li>
                            <li><i class="fas fa-check text-success me-2"></i>pH balanceado</li>
                            <li><i class="fas fa-check text-success me-2"></i>Crescimento saudável</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Recomendações
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li><i class="fas fa-arrow-right text-warning me-2"></i>Aplicar fertilizante em 3 dias</li>
                            <li><i class="fas fa-arrow-right text-warning me-2"></i>Verificar pragas na próxima semana</li>
                            <li><i class="fas fa-arrow-right text-warning me-2"></i>Monitorar chuvas previstas</li>
                            <li><i class="fas fa-arrow-right text-warning me-2"></i>Preparar para próxima fase</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sensores Conectados -->
        <div class="row">
            <div class="col-12">
                <h6 class="mb-3">
                    <i class="fas fa-satellite-dish me-2"></i>
                    Sensores Conectados
                </h6>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Sensor</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Última Leitura</th>
                                <th>Valor</th>
                                <th>Bateria</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dados simulados - implementar com dados reais -->
                            <tr>
                                <td>Sensor #001</td>
                                <td>Temperatura/Umidade</td>
                                <td><span class="badge bg-success">Online</span></td>
                                <td>{{ now()->format('d/m/Y H:i:s') }}</td>
                                <td>24.5°C / 67%</td>
                                <td><span class="badge bg-success">85%</span></td>
                            </tr>
                            <tr>
                                <td>Sensor #002</td>
                                <td>pH do Solo</td>
                                <td><span class="badge bg-success">Online</span></td>
                                <td>{{ now()->subMinutes(5)->format('d/m/Y H:i:s') }}</td>
                                <td>6.8</td>
                                <td><span class="badge bg-warning">45%</span></td>
                            </tr>
                            <tr>
                                <td>Sensor #003</td>
                                <td>Luminosidade</td>
                                <td><span class="badge bg-danger">Offline</span></td>
                                <td>{{ now()->subHours(2)->format('d/m/Y H:i:s') }}</td>
                                <td>--</td>
                                <td><span class="badge bg-danger">12%</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</x-ui.modal-funcional>
