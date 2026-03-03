<x-ui.modal-funcional
    modalId="relatorioInsumo"
    title="Relatório de Insumo"
    icon="fas fa-file-alt"
    size="modal-xl"
    :item="$insumo ?? null"
    resourceName="insumo"
    :additionalButtons="[
        [
            'tag' => 'button',
            'text' => 'Exportar PDF',
            'class' => 'btn-primary',
            'icon' => 'fas fa-file-pdf',
            'onclick' => 'alert(\'Funcionalidade de exportar PDF será implementada\')'
        ],
        [
            'tag' => 'button',
            'text' => 'Enviar por Email',
            'class' => 'btn-primary',
            'icon' => 'fas fa-envelope',
            'onclick' => 'alert(\'Funcionalidade de enviar email será implementada\')'
        ]
    ]"
>

    @if($insumo)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h4 class="card-title mb-1">{{ $insumo->ds_nome }}</h4>
                        <p class="card-text mb-0">Relatório Completo de Utilização e Performance</p>
                        <small>Gerado em: {{ now()->format('d/m/Y H:i:s') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumo Executivo -->
        <div class="row mb-4">
            <div class="col-12">
                <h6 class="mb-3">
                    <i class="fas fa-chart-pie me-2"></i>
                    Resumo Executivo - Últimos 30 Dias
                </h6>
            </div>
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                        <h6>Consumo Total</h6>
                        <h4 class="text-primary">342
                            {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="fas fa-dollar-sign fa-2x text-success mb-2"></i>
                        <h6>Custo Total</h6>
                        <h4 class="text-success">R$ 1.254,80</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <i class="fas fa-seedling fa-2x text-info mb-2"></i>
                        <h6>Área Tratada</h6>
                        <h4 class="text-info">23,5 ha</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-percentage fa-2x text-warning mb-2"></i>
                        <h6>Eficiência</h6>
                        <h4 class="text-warning">87,3%</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos de Performance -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Consumo Mensal
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="consumoChart"
                            style="height: 200px; background: #f8f9fa; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                            <div style="text-align: center; color: #6c757d;">
                                <i class="fas fa-chart-bar fa-3x mb-2"></i><br>
                                Gráfico de Consumo Mensal<br>
                                <small>(Implementar com Chart.js)</small>
                            </div>
                        </canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>
                            Distribuição por Lavoura
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Lavoura Norte</span>
                            <span>35% (120
                                {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }})</span>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-primary" style="width: 35%"></div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Lavoura Sul</span>
                            <span>28% (96
                                {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }})</span>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-success" style="width: 28%"></div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Lavoura Leste</span>
                            <span>22% (75
                                {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }})</span>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-info" style="width: 22%"></div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Lavoura Oeste</span>
                            <span>15% (51
                                {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }})</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" style="width: 15%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Análise Detalhada -->
        <div class="row mb-4">
            <div class="col-12">
                <h6 class="mb-3">
                    <i class="fas fa-microscope me-2"></i>
                    Análise Detalhada
                </h6>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Período</th>
                                <th>Aplicações</th>
                                <th>Quantidade</th>
                                <th>Custo</th>
                                <th>Área</th>
                                <th>Eficiência</th>
                                <th>Resultado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Semana 1</td>
                                <td>3</td>
                                <td>85 {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}</td>
                                <td>R$ 312,30</td>
                                <td>5,8 ha</td>
                                <td><span class="badge bg-success">92%</span></td>
                                <td><span class="badge bg-success">Excelente</span></td>
                            </tr>
                            <tr>
                                <td>Semana 2</td>
                                <td>4</td>
                                <td>96 {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}</td>
                                <td>R$ 352,80</td>
                                <td>6,2 ha</td>
                                <td><span class="badge bg-success">89%</span></td>
                                <td><span class="badge bg-success">Bom</span></td>
                            </tr>
                            <tr>
                                <td>Semana 3</td>
                                <td>2</td>
                                <td>78 {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}</td>
                                <td>R$ 286,70</td>
                                <td>5,1 ha</td>
                                <td><span class="badge bg-warning">76%</span></td>
                                <td><span class="badge bg-warning">Regular</span></td>
                            </tr>
                            <tr>
                                <td>Semana 4</td>
                                <td>3</td>
                                <td>83 {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}</td>
                                <td>R$ 303,00</td>
                                <td>6,4 ha</td>
                                <td><span class="badge bg-success">94%</span></td>
                                <td><span class="badge bg-success">Excelente</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recomendações -->
        <div class="row">
            <div class="col-md-6">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            Pontos Positivos
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li><i class="fas fa-check text-success me-2"></i>Eficiência geral acima da média</li>
                            <li><i class="fas fa-check text-success me-2"></i>Redução de 15% no consumo vs. mês anterior
                            </li>
                            <li><i class="fas fa-check text-success me-2"></i>Excelente performance na Lavoura Norte</li>
                            <li><i class="fas fa-check text-success me-2"></i>Estoque adequado para próximo mês</li>
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
                            <li><i class="fas fa-arrow-right text-warning me-2"></i>Revisar dosagem na Lavoura Leste</li>
                            <li><i class="fas fa-arrow-right text-warning me-2"></i>Considerar aplicação preventiva</li>
                            <li><i class="fas fa-arrow-right text-warning me-2"></i>Monitorar clima antes da aplicação</li>
                            <li><i class="fas fa-arrow-right text-warning me-2"></i>Revisar fornecedor atual</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    @else
        <div class="text-center text-muted py-4">
            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
            <p>Insumo não encontrado.</p>
        </div>
    @endif
</x-ui.modal-funcional>
