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
    <p class="text-muted mb-4 text-sm">
        Defina os valores mínimo e máximo aceitáveis para cada parâmetro monitorado nesta lavoura.
        Fora desse intervalo, um alerta será gerado automaticamente. Deixe em branco os parâmetros sem sensor instalado.
    </p>

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
