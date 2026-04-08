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
            <div class="mdl-grid">
                <div class="mdl-cell mdl-cell--6-col">
                    <strong>Nome do Insumo:</strong> {{ $insumo->ds_nome }}
                </div>
                <div class="mdl-cell mdl-cell--6-col">
                    <strong>Tipo de Insumo:</strong> {{ $insumo->tp_insumo ? $insumo->tp_insumo->label() : 'Não informado' }}
                </div>
                <div class="mdl-cell mdl-cell--6-col">
                    <strong>Fabricante:</strong> {{ $insumo->ds_fabricante ?? 'Não informado' }}
                </div>
                <div class="mdl-cell mdl-cell--6-col">
                    <strong>Unidade de Medida:</strong> {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->label() : 'Não informada' }}
                </div>
            </div>
        </fieldset>

        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--4-col">
                <div class="card border-primary mb-3">
                    <div class="card-body text-center">
                        <i class="fas fa-warehouse fa-2x text-primary mb-2"></i>
                        <h5 class="card-title">Estoque Atual</h5>
                        <h3 class="text-primary">
                            {{ number_format($insumo->estoque_atual, 2, ',', '.') }}
                            {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}
                        </h3>
                    </div>
                </div>
            </div>
            <div class="mdl-cell mdl-cell--4-col">
                <div class="card border-warning mb-3">
                    <div class="card-body text-center">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                        <h5 class="card-title">Estoque Mínimo</h5>
                        <h3 class="text-warning">
                            {{ number_format($insumo->nu_estoque_minimo, 2, ',', '.') }}
                            {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}
                        </h3>
                    </div>
                </div>
            </div>
            <div class="mdl-cell mdl-cell--4-col">
                <div class="card border-{{ $insumo->estoque_abaixo_minimo ? 'danger' : 'success' }} mb-3">
                    <div class="card-body text-center">
                        <i class="fas fa-{{ $insumo->estoque_abaixo_minimo ? 'times-circle' : 'check-circle' }} fa-2x text-{{ $insumo->estoque_abaixo_minimo ? 'danger' : 'success' }} mb-2"></i>
                        <h5 class="card-title">Status do Estoque</h5>
                        <h3 class="text-{{ $insumo->estoque_abaixo_minimo ? 'danger' : 'success' }}">
                            {{ $insumo->estoque_abaixo_minimo ? 'Baixo' : 'Normal' }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--12-col">
                <i class="fas fa-history me-2"></i>
                Últimas Movimentações
            </div>
            <div class="mdl-cell mdl-cell--12-col">
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark" style="position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th>Data</th>
                                <th>Tipo</th>
                                <th>Quantidade</th>
                                <th>Saldo</th>
                                <th>Observação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($insumo->movimentacoes && $insumo->movimentacoes->count() > 0)
                                @foreach($insumo->movimentacoes_com_saldo as $movimentacao)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($movimentacao->dt_movimentacao)->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $movimentacao->tipo_class }}">
                                                {{ $movimentacao->tp_controle ? $movimentacao->tp_controle->label() : 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-{{ $movimentacao->tipo_class }}">
                                                {{ $movimentacao->quantidade_formatada }} {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $saldoCorClass = $movimentacao->saldo_calculado >= 0 ? 'success' : 'danger';
                                            @endphp
                                            <strong class="text-{{ $saldoCorClass }}">
                                                {{ number_format($movimentacao->saldo_calculado, 2, ',', '.') }} {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}
                                            </strong>
                                        </td>
                                        <td>
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
                                    <td colspan="5" class="text-center text-muted">
                                        <i class="fas fa-inbox me-2"></i>
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
