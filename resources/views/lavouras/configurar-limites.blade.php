<x-ui.modal-funcional
    modalId="configurarLimites"
    title="Configurar Limites"
    icon="fas fa-sliders-h"
    size="modal-lg"
    :item="$lavoura ?? null"
    resourceName="lavoura"
    :additionalButtons="[
        [
            'tag' => 'button',
            'text' => 'Salvar',
            'class' => 'btn-success',
            'icon' => 'fas fa-save',
            'type' => 'submit',
            'form' => 'formLimites',
        ],
    ]"
>
    <p class="mb-4 text-sm text-muted">
        Defina os valores mínimo e máximo aceitáveis para cada parâmetro monitorado nesta lavoura.
        Fora desse intervalo o sistema gera alerta, e é com essa faixa que o relatório avalia as leituras.
        Deixe em branco os parâmetros sem sensor instalado.
    </p>

    {{-- Preenche os campos com valores de referência da cultura: sem isso a tela
         são 16 campos em branco e a maioria acaba ficando sem configuração. --}}
    <div
        x-data="{
            cultura: '{{ $culturaSugerida }}',
            sugestoes: {{ Js::from($sugestoes) }},
            aplicar() {
                Object.entries(this.sugestoes[this.cultura] || {}).forEach(([tipo, faixa]) => {
                    if (!faixa) return;
                    const min = document.querySelector(`[name='limites[${tipo}][valor_min]']`);
                    const max = document.querySelector(`[name='limites[${tipo}][valor_max]']`);
                    if (min) min.value = faixa.min;
                    if (max) max.value = faixa.max;
                });
            },
        }"
        class="mb-5 rounded-xl border border-subtle bg-subtle p-4"
    >
        <div class="flex flex-wrap items-end gap-3">
            <div class="min-w-[200px] flex-1">
                <label for="culturaSugerida" class="form-label">Usar valores de referência de</label>
                <select id="culturaSugerida" x-model="cultura" class="form-control">
                    @foreach ($culturas as $chave => $rotulo)
                        <option value="{{ $chave }}">{{ $rotulo }}</option>
                    @endforeach
                </select>
            </div>
            <button type="button" class="btn btn-secondary" @click="aplicar()">
                <i class="fas fa-wand-magic-sparkles"></i>
                Preencher
            </button>
        </div>
        <p class="mt-2 text-xs text-muted">
            Valores de partida da literatura agronômica. Ajuste conforme a análise de solo e a
            recomendação do seu agrônomo — nada é salvo até você clicar em Salvar.
        </p>
    </div>

    <x-form.form-modal
        action="{{ route('lavouras.limites.salvar', $lavoura->id_lavoura) }}"
        method="POST"
        id="formLimites"
        title="Limites por Parâmetro"
    >
        @foreach($tiposSensor as $tipo)
            <div class="col-span-12 md:col-span-6">
                <span class="form-label">{{ $tipo->label() }}@if($tipo->unidade()) ({{ $tipo->unidade() }}) @endif</span>
                <div class="grid grid-cols-2 gap-2">
                    <x-form.input
                        name="limites[{{ $tipo->value }}][valor_min]"
                        label="Mínimo"
                        type="number"
                        step="0.01"
                        :value="$limites[$tipo->value]->valor_min ?? ''"
                    />
                    <x-form.input
                        name="limites[{{ $tipo->value }}][valor_max]"
                        label="Máximo"
                        type="number"
                        step="0.01"
                        :value="$limites[$tipo->value]->valor_max ?? ''"
                    />
                </div>
            </div>
        @endforeach
    </x-form.form-modal>
</x-ui.modal-funcional>
