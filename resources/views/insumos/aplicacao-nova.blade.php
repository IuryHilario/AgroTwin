<x-ui.modal-funcional
    modalId="novaAplicacaoInsumo"
    title="Nova Aplicação de Insumo"
    icon="fas fa-spray-can"
    size="modal-lg"
    :item="$aplicacao ?? null"
    resourceName="insumo"
    :additionalButtons="[
        [
            'tag' => 'button',
            'text' => 'Voltar às Aplicações',
            'class' => 'btn-secondary',
            'icon' => 'fas fa-arrow-left',
            'type' => 'button',
            'data-action' => 'voltar-aplicacao',
            'data-url' => isset($aplicacao) ? route('insumos.aplicacao', $aplicacao->id_insumo) : '#'
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
    @if($aplicacao)
        <!-- Informações do Insumo -->
        <div class="mb-4 grid grid-cols-12 gap-4">
            <div class="card col-span-12 bg-subtle">
                <div class="p-4">
                    <h6 class="mb-3 flex items-center font-semibold">
                        <i class="fas fa-flask mr-2"></i>
                        Insumo Selecionado
                    </h6>
                    <div class="grid grid-cols-12 gap-4 text-sm">
                        <div class="col-span-12 md:col-span-6">
                            <strong>Nome:</strong> {{ $aplicacao->ds_nome }}
                        </div>
                        <div class="col-span-12 md:col-span-6">
                            <strong>Tipo:</strong> {{ $aplicacao->tp_insumo ? $aplicacao->tp_insumo->label() : 'Não informado' }}
                        </div>
                        <div class="col-span-12 mt-2 md:col-span-6">
                            <strong>Estoque Disponível:</strong>
                            <span class="rounded-full px-2 py-1 text-xs font-semibold text-white {{ $aplicacao->estoque_atual > 0 ? 'bg-green-600' : 'bg-red-600' }}">
                                {{ number_format($aplicacao->estoque_atual, 2, ',', '.') }}
                                {{ $aplicacao->tp_unidade_medida ? $aplicacao->tp_unidade_medida->value : 'UN' }}
                            </span>
                        </div>
                        <div class="col-span-12 mt-2 md:col-span-6">
                            <strong>Unidade:</strong> {{ $aplicacao->tp_unidade_medida ? $aplicacao->tp_unidade_medida->label() : 'Não informada' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulário de Nova Aplicação -->
        <x-form.form-modal
            action="{{ route('insumos.aplicacao.store', $aplicacao->id_insumo) }}"
            method="POST"
            id="formNovaAplicacao"
            title="Registrar Nova Aplicação"
        >
            <!-- Dados da Aplicação -->
            <div class="col-span-12">
                <h6 class="mb-3 flex items-center text-muted">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    Dados da Aplicação
                </h6>
            </div>

            <div class="col-span-12">
                <x-form.input
                    name="dt_aplicacao"
                    label="Data Aplicação"
                    type="datetime-local"
                    :value="now()->format('Y-m-d H:i')"
                    required
                />
            </div>
            <!-- Localização -->
            <div class="col-span-12">
                <h6 class="mb-3 mt-3 flex items-center text-muted">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    Localização
                </h6>
            </div>

            <div class="col-span-12 md:col-span-8">
                <x-form.select
                    name="id_lavoura"
                    label="Lavoura"
                    :options="['' => 'Selecione uma lavoura'] + $lavouras->pluck('ds_cultura', 'id_lavoura')->toArray()"
                    required
                />
            </div>
            <div class="col-span-12 md:col-span-4">
                <x-form.input
                    name="nu_area_aplicada"
                    label="Área Aplicada (ha)"
                    type="number"
                    step="0.01"
                    min="0.01"
                    placeholder="0.00"
                    required
                />
            </div>

            <!-- Dosagem -->
            <div class="col-span-12">
                <h6 class="mb-3 mt-3 flex items-center text-muted">
                    <i class="fas fa-balance-scale mr-2"></i>
                    Dosagem e Quantidade
                </h6>
            </div>

            <div class="col-span-12 md:col-span-4">
                <x-form.input
                    name="nu_quantidade_aplicada"
                    label="Quantidade Aplicada"
                    type="number"
                    step="0.01"
                    min="0.01"
                    placeholder="0.00"
                    required
                />
            </div>
            <div class="col-span-12 md:col-span-4">
                <x-form.input
                    name="nu_dosagem_hectare"
                    label="Dosagem Hectare"
                    type="number"
                    step="0.01"
                    min="0.01"
                    placeholder="0.00"
                    readonly
                />
            </div>
            <div class="col-span-12 md:col-span-4">
                <x-form.input
                    name="nu_concentracao"
                    label="Concentração (%)"
                    type="number"
                    step="0.01"
                    min="0"
                    max="100"
                    placeholder="0.00"
                />
            </div>

            <!-- Equipamento e Método -->
            <div class="col-span-12">
                <h6 class="mb-3 mt-3 flex items-center text-muted">
                    <i class="fas fa-tools mr-2"></i>
                    Equipamento e Método
                </h6>
            </div>

            <div class="col-span-12 md:col-span-6">
                <x-form.select
                    name="tp_metodo_aplicacao"
                    label="Método de Aplicação"
                    :options="\App\Enums\TipoMetodoAplicacao::toSelectArray(true)"
                    required
                />
            </div>
            <div class="col-span-12 md:col-span-6">
                <x-form.input
                    name="ds_equipamento"
                    label="Equipamento Utilizado"
                    placeholder="Ex: Pulverizador 2000L, Drone DJI T20"
                />
            </div>

            <!-- Responsável -->
            <div class="col-span-12 md:col-span-6">
                <x-form.input
                    name="ds_responsavel"
                    label="Responsável pela Aplicação"
                    required
                />
            </div>
            <div class="col-span-12 md:col-span-6">
                <x-form.select
                    name="tp_finalidade"
                    label="Finalidade"
                    :options="\App\Enums\TipoFinalidade::toSelectArray(true)"
                />
            </div>

            <!-- Observações -->
            <div class="col-span-12">
                <x-form.input
                    name="ds_observacoes"
                    label="Observações"
                    type="textarea"
                    rows="3"
                    placeholder="Observações sobre a aplicação, resultados esperados, etc."
                />
            </div>

            <!-- Alertas -->
            <div id="alertaEstoqueInsuficiente" class="col-span-12 hidden rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-300">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Atenção:</strong> A quantidade informada é superior ao estoque disponível.
            </div>

            <div id="alertaCondicoesDesfavoraveis" class="col-span-12 hidden rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-700 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                <i class="fas fa-exclamation-triangle mr-2"></i>
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
                const estoqueDisponivel = {{ $aplicacao->estoque_atual }};

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
                        alertaEstoque.classList.remove('hidden');
                    } else {
                        alertaEstoque.classList.add('hidden');
                    }
                }

                function verificarCondicoes() {
                    const vento = parseFloat(document.querySelector('input[name="velocidade_vento"]').value) || 0;
                    const umidade = parseFloat(document.querySelector('input[name="umidade"]').value) || 0;

                    let condicoesRuins = false;

                    if (vento > 10) condicoesRuins = true;
                    if (umidade < 40 || umidade > 95) condicoesRuins = true;

                    if (condicoesRuins) {
                        alertaCondicoes.classList.remove('hidden');
                    } else {
                        alertaCondicoes.classList.add('hidden');
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
