<x-ui.modal-funcional
    modalId="novaAplicacaoInsumo"
    title="Nova Aplicação de Insumo"
    icon="fas fa-spray-can"
    size="modal-lg"
    :item="$insumo ?? null"
    resourceName="insumo"
    :additionalButtons="[
        [
            'tag' => 'button',
            'text' => 'Voltar às Aplicações',
            'class' => 'btn-secondary',
            'icon' => 'fas fa-arrow-left',
            'type' => 'button',
            'data-action' => 'voltar-aplicacao',
            'data-url' => isset($insumo) ? route('insumos.aplicacao', $insumo->id_insumo) : '#'
        ],
        [
            'tag' => 'button',
            'text' => 'Limpar',
            'class' => 'btn-warning',
            'icon' => 'fas fa-eraser',
            'type' => 'reset',
            'form' => 'formNovaAplicacao'
        ],
        [
            'tag' => 'button',
            'text' => 'Salvar',
            'class' => 'btn-success',
            'icon' => 'fas fa-save',
            'type' => 'submit',
            'form' => 'formNovaAplicacao'
        ],
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
                            Insumo Selecionado
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Nome:</strong> {{ $insumo->ds_nome }}
                            </div>
                            <div class="col-md-6">
                                <strong>Tipo:</strong> {{ $insumo->tp_insumo ? $insumo->tp_insumo->label() : 'Não informado' }}
                            </div>
                            <div class="col-md-6 mt-2">
                                <strong>Estoque Disponível:</strong>
                                <span class="badge bg-{{ $insumo->estoque_atual > 0 ? 'success' : 'danger' }}">
                                    {{ number_format($insumo->estoque_atual, 2, ',', '.') }}
                                    {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN' }}
                                </span>
                            </div>
                            <div class="col-md-6 mt-2">
                                <strong>Unidade:</strong> {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->label() : 'Não informada' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulário de Nova Aplicação -->
        <x-form.form-modal
            action="#"
            method="POST"
            class="mdl-grid"
            id="formNovaAplicacao"
            title="Registrar Nova Aplicação"
        >
            <!-- Dados da Aplicação -->
            <div class="mdl-cell mdl-cell--12-col">
                <h6 class="text-muted mb-3">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Dados da Aplicação
                </h6>
            </div>

            <div class="mdl-cell mdl-cell--6-col">
                <x-form.input
                    name="data_aplicacao"
                    label="Data da Aplicação"
                    type="date"
                    :value="now()->format('Y-m-d')"
                    required
                />
            </div>
            <div class="mdl-cell mdl-cell--6-col">
                <x-form.input
                    name="hora_aplicacao"
                    label="Hora da Aplicação"
                    type="time"
                    :value="now()->format('H:i')"
                    required
                />
            </div>

            <!-- Localização -->
            <div class="mdl-cell mdl-cell--12-col">
                <h6 class="text-muted mb-3 mt-3">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    Localização
                </h6>
            </div>

            <div class="mdl-cell mdl-cell--8-col">
                <x-form.select
                    name="lavoura_id"
                    label="Lavoura"
                    :options="[
                        '' => 'Selecione uma lavoura...',
                        1 => 'Lavoura Norte',
                        2 => 'Lavoura Sul',
                        3 => 'Lavoura Leste',
                        4 => 'Lavoura Oeste',
                        5 => 'Lavoura Centro'
                    ]"
                    required
                />
            </div>
            <div class="mdl-cell mdl-cell--4-col">
                <x-form.input
                    name="area_aplicada"
                    label="Área Aplicada (ha)"
                    type="number"
                    step="0.01"
                    min="0.01"
                    placeholder="0.00"
                    required
                />
            </div>

            <!-- Dosagem -->
            <div class="mdl-cell mdl-cell--12-col">
                <h6 class="text-muted mb-3 mt-3">
                    <i class="fas fa-balance-scale me-2"></i>
                    Dosagem e Quantidade
                </h6>
            </div>

            <div class="mdl-cell mdl-cell--4-col">
                <x-form.input
                    name="quantidade_aplicada"
                    label="Quantidade Aplicada"
                    type="number"
                    step="0.01"
                    min="0.01"
                    placeholder="0.00"
                    required
                />
            </div>
            <div class="mdl-cell mdl-cell--4-col">
                <x-form.input
                    name="dosagem_hectare"
                    label="Dosagem por Hectare"
                    type="number"
                    step="0.01"
                    min="0.01"
                    placeholder="0.00"
                    readonly
                />
            </div>
            <div class="mdl-cell mdl-cell--4-col">
                <x-form.input
                    name="concentracao"
                    label="Concentração (%)"
                    type="number"
                    step="0.01"
                    min="0"
                    max="100"
                    placeholder="0.00"
                />
            </div>

            <!-- Equipamento e Método -->
            <div class="mdl-cell mdl-cell--12-col">
                <h6 class="text-muted mb-3 mt-3">
                    <i class="fas fa-tools me-2"></i>
                    Equipamento e Método
                </h6>
            </div>

            <div class="mdl-cell mdl-cell--6-col">
                <x-form.select
                    name="metodo_aplicacao"
                    label="Método de Aplicação"
                    :options="[
                        '' => 'Selecione...',
                        'pulverizacao_terrestre' => 'Pulverização Terrestre',
                        'pulverizacao_aerea' => 'Pulverização Aérea',
                        'aplicacao_localizada' => 'Aplicação Localizada',
                        'fertirrigacao' => 'Fertirrigação'
                    ]"
                    required
                />
            </div>
            <div class="mdl-cell mdl-cell--6-col">
                <x-form.input
                    name="equipamento"
                    label="Equipamento Utilizado"
                    placeholder="Ex: Pulverizador 2000L, Drone DJI T20"
                />
            </div>

            <!-- Responsável -->
            <div class="mdl-cell mdl-cell--6-col">
                <x-form.input
                    name="responsavel"
                    label="Responsável pela Aplicação"
                    :value="auth()->user()->name"
                    required
                />
            </div>
            <div class="mdl-cell mdl-cell--6-col">
                <x-form.select
                    name="finalidade"
                    label="Finalidade"
                    :options="[
                        '' => 'Selecione...',
                        'preventiva' => 'Preventiva',
                        'curativa' => 'Curativa',
                        'nutricional' => 'Nutricional',
                        'crescimento' => 'Crescimento',
                        'manutencao' => 'Manutenção'
                    ]"
                />
            </div>

            <!-- Observações -->
            <div class="mdl-cell mdl-cell--12-col">
                <x-form.input
                    name="observacoes"
                    label="Observações"
                    type="textarea"
                    rows="3"
                    placeholder="Observações sobre a aplicação, resultados esperados, etc."
                />
            </div>

            <!-- Alertas -->
            <div id="alertaEstoqueInsuficiente" class="alert alert-danger d-none">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Atenção:</strong> A quantidade informada é superior ao estoque disponível.
            </div>

            <div id="alertaCondicoesDesfavoraveis" class="alert alert-warning d-none">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Condições Desfavoráveis:</strong> As condições climáticas podem não ser ideais para aplicação.
            </div>
        </x-form.form-modal>

        <!-- Script para cálculos automáticos -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const quantidadeInput = document.querySelector('input[name="quantidade_aplicada"]');
                const areaInput = document.querySelector('input[name="area_aplicada"]');
                const dosagemInput = document.querySelector('input[name="dosagem_hectare"]');
                const alertaEstoque = document.getElementById('alertaEstoqueInsuficiente');
                const alertaCondicoes = document.getElementById('alertaCondicoesDesfavoraveis');
                const estoqueDisponivel = {{ $insumo->estoque_atual }};

                function calcularDosagem() {
                    const quantidade = parseFloat(quantidadeInput.value) || 0;
                    const area = parseFloat(areaInput.value) || 0;

                    if (quantidade > 0 && area > 0) {
                        const dosagem = quantidade / area;
                        dosagemInput.value = dosagem.toFixed(2);
                    } else {
                        dosagemInput.value = '';
                    }

                    // Verifica estoque
                    if (quantidade > estoqueDisponivel) {
                        alertaEstoque.classList.remove('d-none');
                    } else {
                        alertaEstoque.classList.add('d-none');
                    }
                }

                function verificarCondicoes() {
                    const vento = parseFloat(document.querySelector('input[name="velocidade_vento"]').value) || 0;
                    const umidade = parseFloat(document.querySelector('input[name="umidade"]').value) || 0;

                    let condicoesRuins = false;

                    if (vento > 10) condicoesRuins = true;
                    if (umidade < 40 || umidade > 95) condicoesRuins = true;

                    if (condicoesRuins) {
                        alertaCondicoes.classList.remove('d-none');
                    } else {
                        alertaCondicoes.classList.add('d-none');
                    }
                }

                quantidadeInput.addEventListener('input', calcularDosagem);
                areaInput.addEventListener('input', calcularDosagem);

                document.querySelector('input[name="velocidade_vento"]').addEventListener('input', verificarCondicoes);
                document.querySelector('input[name="umidade"]').addEventListener('input', verificarCondicoes);
            });
        </script>
    @endif
</x-ui.modal-funcional>
