<x-ui.modal-funcional
    modalId="novaMovimentacaoInsumo"
    title="Nova Movimentação de Estoque"
    icon="fas fa-plus-circle"
    size="modal-lg"
    :item="$insumo ?? null"
    resourceName="insumo"
    :additionalButtons="[
        [
            'tag' => 'button',
            'text' => 'Voltar ao Estoque',
            'class' => 'btn-secondary',
            'icon' => 'fas fa-arrow-left',
            'type' => 'button',
            'data-action' => 'voltar-estoque',
            'data-url' => isset($insumo) ? route('insumos.estoque', $insumo->id_insumo) : '#'
        ],
        [
            'tag' => 'button',
            'text' => 'Limpar',
            'class' => 'btn-warning',
            'icon' => 'fas fa-eraser',
            'type' => 'reset',
            'form' => 'formNovaMovimentacao'
        ],
        [
            'tag' => 'button',
            'text' => 'Salvar',
            'class' => 'btn-success',
            'icon' => 'fas fa-save',
            'type' => 'submit',
            'form' => 'formNovaMovimentacao'
        ],
    ]"
>
    @if($insumo)
        <!-- Informações do Insumo -->
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

        <!-- Formulário de Nova Movimentação -->
        <x-form.form-modal
            action="{{ route('insumos.estoque.store', $insumo->id_insumo) }}"
            method="POST"
            class="mdl-grid"
            id="formNovaMovimentacao"
            title="Nova Movimentação"
        >
            <div class="mdl-cell mdl-cell--6-col">
                <x-form.select
                    name="tp_controle"
                    label="Tipo de Movimentação"
                    :options="\App\Enums\TipoMovimentacao::toSelectArray(true)"
                    required
                />
            </div>
            <div class="mdl-cell mdl-cell--6-col">
                <x-form.input
                    name="nu_quantidade"
                    label="Quantidade"
                    type="number"
                    step="0.01"
                    min="0.01"
                    placeholder="0.00"
                    required
                />
            </div>
            <div class="mdl-cell mdl-cell--6-col">
                <x-form.input
                    name="dt_movimentacao"
                    label="Data da Movimentação"
                    type="date"
                    :value="now()->format('Y-m-d')"
                    required
                />
            </div>
            <div class="mdl-cell mdl-cell--6-col">
                <x-form.input
                    name="vl_unitario"
                    label="Valor Unitário"
                    type="number"
                    step="0.01"
                    min="0"
                    placeholder="0.00"
                    required
                />
            </div>
            <div class="mdl-cell mdl-cell--12-col">
                <x-form.input
                    name="ds_documento"
                    label="Documento/Referência (Opcional)"
                    placeholder="Ex: NF 12345, Ordem 001"
                />
            </div>
            <div class="mdl-cell mdl-cell--12-col">
                <x-form.input
                    name="ds_fornecedor"
                    label="Fornecedor/Destino (Opcional)"
                    placeholder="Nome do fornecedor ou destino"
                />
            </div>
            <div class="mdl-cell mdl-cell--12-col">
                <x-form.input
                    name="ds_observacao"
                    label="Observações (Opcional)"
                    type="textarea"
                    rows="3"
                />
            </div>

            <!-- Alerta de Validação de Estoque -->
            <div id="alertaEstoque" class="alert alert-warning d-none">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Atenção:</strong> A quantidade informada resultará em estoque negativo.
            </div>
        </x-form.form-modal>
    @endif
</x-ui.modal-funcional>
