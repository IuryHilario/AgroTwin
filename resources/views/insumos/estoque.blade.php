<x-ui.modal-funcional
    modalId="estoqueInsumo"
    title="Estoque"
    icon="fas fa-boxes"
    size="modal-lg"
    :item="$insumo ?? null"
    resourceName="insumo"
    :additionalButtons="[
        [
            'tag' => 'button',
            'text' => 'Nova Movimentação',
            'class' => 'btn-success',
            'icon' => 'fas fa-plus',
            'type' => 'button',
            'data-action' => 'nova-movimentacao',
            'data-url' => isset($insumo) ? route('insumos.estoque.create', $insumo->id_insumo) : '#'
        ]
    ]"
>
    <!-- Conteúdo específico do estoque -->
    @if($insumo)
        <fieldset>
            <legend>Informações do Insumo</legend>
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 md:col-span-6">
                    <strong>Nome do Insumo:</strong> {{ $insumo->ds_nome }}
                </div>
                <div class="col-span-12 md:col-span-6">
                    <strong>Tipo de Insumo:</strong> {{ $insumo->tp_insumo ? $insumo->tp_insumo->label() : 'Não informado' }}
                </div>
                <div class="col-span-12 md:col-span-6">
                    <strong>Fabricante:</strong> {{ $insumo->ds_fabricante ?? 'Não informado' }}
                </div>
                <div class="col-span-12 md:col-span-6">
                    <strong>Unidade de Medida:</strong> {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->label() : 'Não informada' }}
                </div>
            </div>
        </fieldset>

        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 md:col-span-4">
                <div class="card mb-3 border-2 border-blue-500">
                    <div class="p-4 text-center">
                        <i class="fas fa-warehouse mb-2 text-2xl text-blue-600"></i>
                        <h5 class="font-semibold">Estoque Atual</h5>
                        <h3 class="text-xl font-bold text-blue-600">
                            {{ number_format($insumo->estoque_atual, 2, ',', '.') }}
                            {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-span-12 md:col-span-4">
                <div class="card mb-3 border-2 border-amber-500">
                    <div class="p-4 text-center">
                        <i class="fas fa-exclamation-triangle mb-2 text-2xl text-amber-500"></i>
                        <h5 class="font-semibold">Estoque Mínimo</h5>
                        <h3 class="text-xl font-bold text-amber-500">
                            {{ number_format($insumo->nu_estoque_minimo, 2, ',', '.') }}
                            {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-span-12 md:col-span-4">
                <div class="card mb-3 border-2 {{ $insumo->estoque_abaixo_minimo ? 'border-red-500' : 'border-green-500' }}">
                    <div class="p-4 text-center">
                        <i class="fas fa-{{ $insumo->estoque_abaixo_minimo ? 'times-circle' : 'check-circle' }} mb-2 text-2xl {{ $insumo->estoque_abaixo_minimo ? 'text-red-600' : 'text-green-600' }}"></i>
                        <h5 class="font-semibold">Status do Estoque</h5>
                        <h3 class="text-xl font-bold {{ $insumo->estoque_abaixo_minimo ? 'text-red-600' : 'text-green-600' }}">
                            {{ $insumo->estoque_abaixo_minimo ? 'Baixo' : 'Normal' }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 flex items-center">
                <i class="fas fa-history mr-2"></i>
                Últimas Movimentações
            </div>
            <div class="col-span-12">
                <div class="max-h-[300px] overflow-y-auto rounded-xl shadow-[0_2px_10px_rgba(0,0,0,0.08)]">
                    <table class="w-full border-collapse text-left text-sm">
                        <thead class="sticky top-0 z-10 bg-gray-800 text-white">
                            <tr>
                                <th class="px-4 py-3">Data</th>
                                <th class="px-4 py-3">Tipo</th>
                                <th class="px-4 py-3">Quantidade</th>
                                <th class="px-4 py-3">Saldo</th>
                                <th class="px-4 py-3">Observação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($insumo->movimentacoes && $insumo->movimentacoes->count() > 0)
                                @foreach($insumo->movimentacoes_com_saldo as $movimentacao)
                                    <tr class="border-t border-subtle hover-surface">
                                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($movimentacao->dt_movimentacao)->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3">
                                            <span class="rounded-full bg-{{ $movimentacao->tipo_class }}-600 px-2 py-1 text-xs font-semibold text-white">
                                                {{ $movimentacao->tp_controle ? $movimentacao->tp_controle->label() : 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="text-{{ $movimentacao->tipo_class }}-600">
                                                {{ $movimentacao->quantidade_formatada }} {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @php
                                                $saldoCorClass = $movimentacao->saldo_calculado >= 0 ? 'green' : 'red';
                                            @endphp
                                            <strong class="text-{{ $saldoCorClass }}-600">
                                                {{ number_format($movimentacao->saldo_calculado, 2, ',', '.') }} {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}
                                            </strong>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($movimentacao->ds_documento)
                                                {{ $movimentacao->ds_documento }}
                                                @if($movimentacao->ds_fornecedor)
                                                    <br><small class="text-muted">{{ $movimentacao->ds_fornecedor }}</small>
                                                @endif
                                            @elseif($movimentacao->ds_observacao)
                                                {!! nl2br(e($movimentacao->ds_observacao)) !!}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-center text-muted">
                                        <i class="fas fa-inbox mr-2"></i>
                                        Nenhuma movimentação registrada
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</x-ui.modal-funcional>
