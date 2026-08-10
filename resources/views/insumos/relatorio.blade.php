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
        <div class="mb-4 grid grid-cols-12 gap-4">
            <div class="card col-span-12 bg-green-600 text-white">
                <div class="p-4 text-center">
                    <h4 class="mb-1 text-xl font-semibold">{{ $insumo->ds_nome }}</h4>
                    <p class="mb-0">Relatório Completo de Utilização e Performance</p>
                    <small>Gerado em: {{ now()->format('d/m/Y H:i:s') }}</small>
                </div>
            </div>
        </div>

        <!-- Resumo Executivo -->
        <div class="mb-4 grid grid-cols-12 gap-4">
            <div class="col-span-12">
                <h6 class="mb-3 flex items-center font-semibold">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Resumo Executivo - Últimos 30 Dias
                </h6>
            </div>
            <div class="col-span-12 md:col-span-3">
                <div class="card border-2 border-blue-500">
                    <div class="p-4 text-center">
                        <i class="fas fa-shopping-cart mb-2 text-2xl text-blue-600"></i>
                        <h6>Consumo Total</h6>
                        <h4 class="text-xl font-bold text-blue-600">342
                            {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-span-12 md:col-span-3">
                <div class="card border-2 border-green-500">
                    <div class="p-4 text-center">
                        <i class="fas fa-dollar-sign mb-2 text-2xl text-green-600"></i>
                        <h6>Custo Total</h6>
                        <h4 class="text-xl font-bold text-green-600">R$ 1.254,80</h4>
                    </div>
                </div>
            </div>
            <div class="col-span-12 md:col-span-3">
                <div class="card border-2 border-sky-500">
                    <div class="p-4 text-center">
                        <i class="fas fa-seedling mb-2 text-2xl text-sky-600"></i>
                        <h6>Área Tratada</h6>
                        <h4 class="text-xl font-bold text-sky-600">23,5 ha</h4>
                    </div>
                </div>
            </div>
            <div class="col-span-12 md:col-span-3">
                <div class="card border-2 border-amber-500">
                    <div class="p-4 text-center">
                        <i class="fas fa-percentage mb-2 text-2xl text-amber-500"></i>
                        <h6>Eficiência</h6>
                        <h4 class="text-xl font-bold text-amber-500">87,3%</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos de Performance -->
        <div class="mb-4 grid grid-cols-12 gap-4">
            <div class="col-span-12 md:col-span-6">
                <div class="card">
                    <div class="border-b border-subtle px-4 py-3">
                        <h6 class="flex items-center font-semibold">
                            <i class="fas fa-chart-line mr-2"></i>
                            Consumo Mensal
                        </h6>
                    </div>
                    <div class="p-4">
                        <canvas id="consumoChart" class="flex h-[200px] items-center justify-center rounded-md bg-gray-100 dark:bg-gray-900">
                            <div class="text-center text-muted">
                                <i class="fas fa-chart-bar mb-2 block text-3xl"></i>
                                Gráfico de Consumo Mensal<br>
                                <small>(Implementar com Chart.js)</small>
                            </div>
                        </canvas>
                    </div>
                </div>
            </div>
            <div class="col-span-12 md:col-span-6">
                <div class="card">
                    <div class="border-b border-subtle px-4 py-3">
                        <h6 class="flex items-center font-semibold">
                            <i class="fas fa-chart-pie mr-2"></i>
                            Distribuição por Lavoura
                        </h6>
                    </div>
                    <div class="p-4">
                        <div class="mb-2 flex items-center justify-between">
                            <span>Lavoura Norte</span>
                            <span>35% (120
                                {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }})</span>
                        </div>
                        <div class="mb-3 h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                            <div class="h-full bg-blue-600" style="width: 35%"></div>
                        </div>

                        <div class="mb-2 flex items-center justify-between">
                            <span>Lavoura Sul</span>
                            <span>28% (96
                                {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }})</span>
                        </div>
                        <div class="mb-3 h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                            <div class="h-full bg-green-600" style="width: 28%"></div>
                        </div>

                        <div class="mb-2 flex items-center justify-between">
                            <span>Lavoura Leste</span>
                            <span>22% (75
                                {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }})</span>
                        </div>
                        <div class="mb-3 h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                            <div class="h-full bg-sky-500" style="width: 22%"></div>
                        </div>

                        <div class="mb-2 flex items-center justify-between">
                            <span>Lavoura Oeste</span>
                            <span>15% (51
                                {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }})</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                            <div class="h-full bg-amber-500" style="width: 15%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Análise Detalhada -->
        <div class="mb-4 grid grid-cols-12 gap-4">
            <div class="col-span-12">
                <h6 class="mb-3 flex items-center font-semibold">
                    <i class="fas fa-microscope mr-2"></i>
                    Análise Detalhada
                </h6>
                <div class="overflow-x-auto rounded-xl shadow-[0_2px_10px_rgba(0,0,0,0.08)]">
                    <table class="w-full border-collapse text-left text-sm">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="px-4 py-3">Período</th>
                                <th class="px-4 py-3">Aplicações</th>
                                <th class="px-4 py-3">Quantidade</th>
                                <th class="px-4 py-3">Custo</th>
                                <th class="px-4 py-3">Área</th>
                                <th class="px-4 py-3">Eficiência</th>
                                <th class="px-4 py-3">Resultado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t border-subtle hover-surface">
                                <td class="px-4 py-3">Semana 1</td>
                                <td class="px-4 py-3">3</td>
                                <td class="px-4 py-3">85 {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}</td>
                                <td class="px-4 py-3">R$ 312,30</td>
                                <td class="px-4 py-3">5,8 ha</td>
                                <td class="px-4 py-3"><span class="rounded-full bg-green-600 px-2 py-1 text-xs font-semibold text-white">92%</span></td>
                                <td class="px-4 py-3"><span class="rounded-full bg-green-600 px-2 py-1 text-xs font-semibold text-white">Excelente</span></td>
                            </tr>
                            <tr class="border-t border-subtle hover-surface">
                                <td class="px-4 py-3">Semana 2</td>
                                <td class="px-4 py-3">4</td>
                                <td class="px-4 py-3">96 {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}</td>
                                <td class="px-4 py-3">R$ 352,80</td>
                                <td class="px-4 py-3">6,2 ha</td>
                                <td class="px-4 py-3"><span class="rounded-full bg-green-600 px-2 py-1 text-xs font-semibold text-white">89%</span></td>
                                <td class="px-4 py-3"><span class="rounded-full bg-green-600 px-2 py-1 text-xs font-semibold text-white">Bom</span></td>
                            </tr>
                            <tr class="border-t border-subtle hover-surface">
                                <td class="px-4 py-3">Semana 3</td>
                                <td class="px-4 py-3">2</td>
                                <td class="px-4 py-3">78 {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}</td>
                                <td class="px-4 py-3">R$ 286,70</td>
                                <td class="px-4 py-3">5,1 ha</td>
                                <td class="px-4 py-3"><span class="rounded-full bg-amber-500 px-2 py-1 text-xs font-semibold text-white">76%</span></td>
                                <td class="px-4 py-3"><span class="rounded-full bg-amber-500 px-2 py-1 text-xs font-semibold text-white">Regular</span></td>
                            </tr>
                            <tr class="border-t border-subtle hover-surface">
                                <td class="px-4 py-3">Semana 4</td>
                                <td class="px-4 py-3">3</td>
                                <td class="px-4 py-3">83 {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}</td>
                                <td class="px-4 py-3">R$ 303,00</td>
                                <td class="px-4 py-3">6,4 ha</td>
                                <td class="px-4 py-3"><span class="rounded-full bg-green-600 px-2 py-1 text-xs font-semibold text-white">94%</span></td>
                                <td class="px-4 py-3"><span class="rounded-full bg-green-600 px-2 py-1 text-xs font-semibold text-white">Excelente</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recomendações -->
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 md:col-span-6">
                <div class="card border-2 border-green-500">
                    <div class="rounded-t-2xl bg-green-600 px-4 py-3 text-white">
                        <h6 class="flex items-center font-semibold">
                            <i class="fas fa-check-circle mr-2"></i>
                            Pontos Positivos
                        </h6>
                    </div>
                    <div class="p-4">
                        <ul class="m-0 list-none space-y-2 text-sm">
                            <li><i class="fas fa-check mr-2 text-green-600"></i>Eficiência geral acima da média</li>
                            <li><i class="fas fa-check mr-2 text-green-600"></i>Redução de 15% no consumo vs. mês anterior
                            </li>
                            <li><i class="fas fa-check mr-2 text-green-600"></i>Excelente performance na Lavoura Norte</li>
                            <li><i class="fas fa-check mr-2 text-green-600"></i>Estoque adequado para próximo mês</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-span-12 md:col-span-6">
                <div class="card border-2 border-amber-500">
                    <div class="rounded-t-2xl bg-amber-400 px-4 py-3 text-gray-900">
                        <h6 class="flex items-center font-semibold">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Recomendações
                        </h6>
                    </div>
                    <div class="p-4">
                        <ul class="m-0 list-none space-y-2 text-sm">
                            <li><i class="fas fa-arrow-right mr-2 text-amber-500"></i>Revisar dosagem na Lavoura Leste</li>
                            <li><i class="fas fa-arrow-right mr-2 text-amber-500"></i>Considerar aplicação preventiva</li>
                            <li><i class="fas fa-arrow-right mr-2 text-amber-500"></i>Monitorar clima antes da aplicação</li>
                            <li><i class="fas fa-arrow-right mr-2 text-amber-500"></i>Revisar fornecedor atual</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    @else
        <div class="py-8 text-center text-muted">
            <i class="fas fa-exclamation-triangle mb-2 block text-2xl"></i>
            <p>Insumo não encontrado.</p>
        </div>
    @endif
</x-ui.modal-funcional>
