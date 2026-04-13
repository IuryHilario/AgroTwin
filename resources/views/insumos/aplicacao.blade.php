<x-ui.modal-funcional
    modalId="aplicacaoInsumo"
    title="Aplicação de Insumo"
    icon="fas fa-spray-can"
    size="modal-lg"
    :item="$insumo ?? null"
    resourceName="insumo"
    :additionalButtons="[
        [
            'tag' => 'button',
            'text' => 'Nova Aplicação',
            'class' => 'btn-warning',
            'icon' => 'fas fa-plus',
            'type' => 'button',
            'data-action' => 'nova-aplicacao',
            'data-url' => isset($insumo) ? route('insumos.aplicacao.create', $insumo->id_insumo) : '#'
        ]
    ]"
>

    @if($insumo)
        <!-- Informações do Insumo -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="fas fa-flask me-2"></i>
                            Informações do Insumo
                        </h6>
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Nome:</strong> {{ $insumo->ds_nome }}
                            </div>
                            <div class="col-md-3">
                                <strong>Tipo:</strong>
                                {{ $insumo->tp_insumo ? $insumo->tp_insumo->label() : 'Não informado' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Fabricante:</strong> {{ $insumo->ds_fabricante ?? 'Não informado' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Unidade:</strong>
                                {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->label() : 'Não informada' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas de Aplicação -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-check fa-2x text-primary mb-2"></i>
                        <h6 class="card-title">Total de Aplicações</h6>
                        <h4 class="text-primary">12</h4>
                        <small class="text-muted">Este mês</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="fas fa-weight-hanging fa-2x text-success mb-2"></i>
                        <h6 class="card-title">Quantidade Aplicada</h6>
                        <h4 class="text-success">245
                            {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}
                        </h4>
                        <small class="text-muted">Este mês</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <i class="fas fa-map-marked-alt fa-2x text-info mb-2"></i>
                        <h6 class="card-title">Área Coberta</h6>
                        <h4 class="text-info">15,5 ha</h4>
                        <small class="text-muted">Este mês</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-tachometer-alt fa-2x text-warning mb-2"></i>
                        <h6 class="card-title">Dosagem Média</h6>
                        <h4 class="text-warning">15,8
                            {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}/ha
                        </h4>
                        <small class="text-muted">Recomendado: 16/ha</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aplicações Recentes -->
        <div class="row mb-4">
            <div class="col-12">
                <h6 class="mb-3">
                    <i class="fas fa-history me-2"></i>
                    Aplicações Recentes
                </h6>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Data/Hora</th>
                                <th>Lavoura</th>
                                <th>Área (ha)</th>
                                <th>Quantidade</th>
                                <th>Dosagem</th>
                                <th>Clima</th>
                                <th>Responsável</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dados simulados - aqui você implementará a lógica real -->
                            <tr>
                                <td>{{ now()->format('d/m/Y H:i') }}</td>
                                <td>Lavoura Norte</td>
                                <td>2,5</td>
                                <td>40 {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}</td>
                                <td>16 {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}/ha</td>
                                <td><span class="badge bg-success">Ideal</span></td>
                                <td>João Silva</td>
                                <td><span class="badge bg-success">Concluída</span></td>
                            </tr>
                            <tr>
                                <td>{{ now()->subDays(2)->format('d/m/Y H:i') }}</td>
                                <td>Lavoura Sul</td>
                                <td>3,2</td>
                                <td>50 {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}</td>
                                <td>15,6 {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}/ha</td>
                                <td><span class="badge bg-warning">Vento</span></td>
                                <td>Maria Santos</td>
                                <td><span class="badge bg-success">Concluída</span></td>
                            </tr>
                            <tr>
                                <td>{{ now()->subDays(5)->format('d/m/Y H:i') }}</td>
                                <td>Lavoura Leste</td>
                                <td>1,8</td>
                                <td>30 {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}</td>
                                <td>16,7 {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}/ha</td>
                                <td><span class="badge bg-success">Ideal</span></td>
                                <td>João Silva</td>
                                <td><span class="badge bg-success">Concluída</span></td>
                            </tr>
                            <tr>
                                <td>{{ now()->subDays(7)->format('d/m/Y H:i') }}</td>
                                <td>Lavoura Centro</td>
                                <td>4,1</td>
                                <td>65 {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}</td>
                                <td>15,9 {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}/ha</td>
                                <td><span class="badge bg-success">Ideal</span></td>
                                <td>Maria Santos</td>
                                <td><span class="badge bg-success">Concluída</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Gráfico de Eficácia (Simulado) -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Eficácia por Lavoura
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Lavoura Norte</span>
                            <span class="badge bg-success">95%</span>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-success" style="width: 95%"></div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Lavoura Sul</span>
                            <span class="badge bg-success">88%</span>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-success" style="width: 88%"></div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Lavoura Leste</span>
                            <span class="badge bg-warning">72%</span>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-warning" style="width: 72%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Próximas Aplicações Programadas
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Lavoura Oeste</strong><br>
                                    <small class="text-muted">{{ now()->addDays(3)->format('d/m/Y') }} - 08:00</small>
                                </div>
                                <span class="badge bg-primary">Agendada</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Lavoura Norte</strong><br>
                                    <small class="text-muted">{{ now()->addDays(7)->format('d/m/Y') }} - 06:30</small>
                                </div>
                                <span class="badge bg-primary">Agendada</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Lavoura Sul</strong><br>
                                    <small class="text-muted">{{ now()->addDays(10)->format('d/m/Y') }} - 07:00</small>
                                </div>
                                <span class="badge bg-secondary">Planejada</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recomendações baseadas na composição -->
        @if($insumo->ds_composicao)
            <div class="alert alert-info mt-3">
                <h6><i class="fas fa-lightbulb me-2"></i>Recomendações de Aplicação</h6>
                <ul class="mb-0">
                    <li>Aplicar preferencialmente nas primeiras horas da manhã ou final da tarde</li>
                    <li>Evitar aplicação com ventos superiores a 10 km/h</li>
                    <li>Umidade relativa ideal: entre 55% e 95%</li>
                    <li>Baseado na composição: {!! nl2br(e($insumo->ds_composicao)) !!}</li>
                </ul>
            </div>
        @endif

    @else
        <div class="text-center text-muted py-4">
            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
            <p>Insumo não encontrado.</p>
        </div>
    @endif
</x-ui.modal-funcional>
