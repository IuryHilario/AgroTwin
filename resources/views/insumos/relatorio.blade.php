<x-ui.modal-funcional
    modalId="relatorioInsumo"
    title="Relatório de Insumo"
    icon="fas fa-file-alt"
    size="modal-xl"
    :item="$insumo ?? null"
    resourceName="insumo"
    :additionalButtons="[
        [
            'tag' => 'a',
            'text' => 'Exportar PDF',
            'class' => 'btn-primary',
            'icon' => 'fas fa-file-pdf',
            'href' => route('insumos.relatorio.pdf', $insumo->id_insumo ?? 0),
        ],
        [
            'tag' => 'button',
            'text' => 'Enviar por Email',
            'class' => 'btn-primary',
            'icon' => 'fas fa-envelope',
            'onclick' => 'enviarRelatorioInsumoEmail(' . ($insumo->id_insumo ?? 0) . ', this)',
        ]
    ]"
>

    @if($insumo)
        @php
            $unidade = $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN';
        @endphp

        <div class="mb-4 grid grid-cols-12 gap-4">
            <div class="card col-span-12 bg-green-600 text-white">
                <div class="p-4 text-center">
                    <h4 class="mb-1 text-xl font-semibold">{{ $insumo->ds_nome }}</h4>
                    <p class="mb-0">Relatório de Consumo e Custo — Últimos {{ $relatorio['periodoDias'] }} Dias</p>
                    <small>Gerado em: {{ now()->format('d/m/Y H:i:s') }}</small>
                </div>
            </div>
        </div>

        <!-- Resumo Executivo -->
        <div class="mb-4 grid grid-cols-12 gap-4">
            <div class="col-span-12">
                <h6 class="mb-3 flex items-center font-semibold">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Resumo Executivo — Últimos {{ $relatorio['periodoDias'] }} Dias
                </h6>
            </div>
            <div class="col-span-12 md:col-span-3">
                <div class="card border-2 border-blue-500">
                    <div class="p-4 text-center">
                        <i class="fas fa-shopping-cart mb-2 text-2xl text-blue-600"></i>
                        <h6>Consumo Total</h6>
                        <h4 class="text-xl font-bold text-blue-600">{{ number_format($relatorio['consumoTotal'], 2, ',', '.') }} {{ $unidade }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-span-12 md:col-span-3">
                <div class="card border-2 border-green-500">
                    <div class="p-4 text-center">
                        <i class="fas fa-dollar-sign mb-2 text-2xl text-green-600"></i>
                        <h6>Custo Estimado</h6>
                        <h4 class="text-xl font-bold text-green-600">R$ {{ number_format($relatorio['custoTotal'], 2, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-span-12 md:col-span-3">
                <div class="card border-2 border-sky-500">
                    <div class="p-4 text-center">
                        <i class="fas fa-seedling mb-2 text-2xl text-sky-600"></i>
                        <h6>Área Tratada</h6>
                        <h4 class="text-xl font-bold text-sky-600">{{ number_format($relatorio['areaTratada'], 2, ',', '.') }} ha</h4>
                    </div>
                </div>
            </div>
            <div class="col-span-12 md:col-span-3">
                <div class="card border-2 border-amber-500">
                    <div class="p-4 text-center">
                        <i class="fas fa-spray-can mb-2 text-2xl text-amber-500"></i>
                        <h6>Aplicações</h6>
                        <h4 class="text-xl font-bold text-amber-500">{{ $relatorio['totalAplicacoes'] }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Consumo semanal e distribuição por lavoura -->
        <div class="mb-4 grid grid-cols-12 gap-4">
            <div class="col-span-12 md:col-span-6">
                <div class="card">
                    <div class="border-b border-subtle px-4 py-3">
                        <h6 class="flex items-center font-semibold">
                            <i class="fas fa-chart-line mr-2"></i>
                            Consumo por Semana
                        </h6>
                    </div>
                    <div class="p-4">
                        @forelse ($relatorio['serieSemanal'] as $semana)
                            @php
                                $maiorQuantidade = $relatorio['serieSemanal']->max('quantidade') ?: 1;
                                $largura = $semana['quantidade'] > 0 ? max(4, round($semana['quantidade'] / $maiorQuantidade * 100)) : 0;
                            @endphp
                            <div class="mb-2 flex items-center justify-between text-sm">
                                <span>{{ $semana['label'] }}</span>
                                <span>{{ number_format($semana['quantidade'], 2, ',', '.') }} {{ $unidade }}</span>
                            </div>
                            <div class="mb-3 h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                                <div class="h-full bg-blue-600" style="width: {{ $largura }}%"></div>
                            </div>
                        @empty
                            <p class="py-6 text-center text-muted">Nenhuma aplicação registrada no período.</p>
                        @endforelse
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
                        @php
                            $cores = ['bg-blue-600', 'bg-green-600', 'bg-sky-500', 'bg-amber-500', 'bg-purple-500'];
                        @endphp
                        @forelse ($relatorio['distribuicaoPorLavoura'] as $index => $lavoura)
                            <div class="mb-2 flex items-center justify-between">
                                <span>{{ $lavoura['nome'] }}</span>
                                <span>{{ $lavoura['percentual'] }}% ({{ number_format($lavoura['quantidade'], 2, ',', '.') }} {{ $unidade }})</span>
                            </div>
                            <div class="mb-3 h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                                <div class="h-full {{ $cores[$index % count($cores)] }}" style="width: {{ $lavoura['percentual'] }}%"></div>
                            </div>
                        @empty
                            <p class="py-6 text-center text-muted">Nenhuma aplicação registrada no período.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Análise Detalhada -->
        <div class="mb-4 grid grid-cols-12 gap-4">
            <div class="col-span-12">
                <h6 class="mb-3 flex items-center font-semibold">
                    <i class="fas fa-microscope mr-2"></i>
                    Análise Detalhada por Semana
                </h6>
                <div class="overflow-x-auto rounded-xl shadow-[0_2px_10px_rgba(0,0,0,0.08)]">
                    <table class="w-full border-collapse text-left text-sm">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="px-4 py-3">Período</th>
                                <th class="px-4 py-3">Aplicações</th>
                                <th class="px-4 py-3">Quantidade</th>
                                <th class="px-4 py-3">Área</th>
                                <th class="px-4 py-3">Custo Estimado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($relatorio['serieSemanal'] as $semana)
                                <tr class="border-t border-subtle hover-surface">
                                    <td class="px-4 py-3">{{ $semana['label'] }}</td>
                                    <td class="px-4 py-3">{{ $semana['aplicacoes'] }}</td>
                                    <td class="px-4 py-3">{{ number_format($semana['quantidade'], 2, ',', '.') }} {{ $unidade }}</td>
                                    <td class="px-4 py-3">{{ number_format($semana['area'], 2, ',', '.') }} ha</td>
                                    <td class="px-4 py-3">R$ {{ number_format($semana['custo'], 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($insumo->estoque_abaixo_minimo)
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <div class="card border-2 border-red-500">
                        <div class="rounded-t-2xl bg-red-600 px-4 py-3 text-white">
                            <h6 class="flex items-center font-semibold">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Estoque Baixo
                            </h6>
                        </div>
                        <div class="p-4 text-sm">
                            Estoque atual ({{ number_format($insumo->estoque_atual, 2, ',', '.') }} {{ $unidade }}) está abaixo do mínimo configurado ({{ number_format($insumo->nu_estoque_minimo, 2, ',', '.') }} {{ $unidade }}). Considere repor antes da próxima aplicação.
                        </div>
                    </div>
                </div>
            </div>
        @endif

    @else
        <div class="py-8 text-center text-muted">
            <i class="fas fa-exclamation-triangle mb-2 block text-2xl"></i>
            <p>Insumo não encontrado.</p>
        </div>
    @endif
</x-ui.modal-funcional>
