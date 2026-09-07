<x-ui.modal-funcional
    modalId="detalharRecomendacao"
    title="Detalhes da Recomendação"
    icon="fas fa-brain"
    size="modal-lg"
    :item="$recomendacoe ?? null"
    resourceName="recomendação"
>
    @php
        $unidade = $recomendacoe->tp_sensor?->unidade() ?? '';
    @endphp

    <x-ui.detail-section title="Informações Principais" icon="fas fa-info-circle">
        <x-ui.detail-fields :fields="[
            ['label' => 'Lavoura', 'value' => $recomendacoe->lavoura?->ds_cultura],
            ['label' => 'Sensor', 'value' => $recomendacoe->tp_sensor?->label()],
            ['label' => 'Data', 'value' => $recomendacoe->dt_recomendacao->format('d/m/Y H:i')],
        ]" />
    </x-ui.detail-section>

    <x-ui.detail-section title="Recomendação" icon="fas fa-lightbulb">
        <x-ui.detail-card>{{ $recomendacoe->ds_recomendacao }}</x-ui.detail-card>
    </x-ui.detail-section>

    <x-ui.detail-section title="Por que essa recomendação foi gerada" icon="fas fa-circle-question">
        @if($recomendacoe->nu_valor_leitura !== null)
            <x-ui.detail-fields :fields="[
                ['label' => 'Valor Lido', 'value' => $recomendacoe->nu_valor_leitura . $unidade],
                ['label' => 'Limite Mínimo', 'value' => $recomendacoe->nu_limite_min !== null ? $recomendacoe->nu_limite_min . $unidade : 'Não configurado'],
                ['label' => 'Limite Máximo', 'value' => $recomendacoe->nu_limite_max !== null ? $recomendacoe->nu_limite_max . $unidade : 'Não configurado'],
                ['label' => 'Motivo', 'value' => $recomendacoe->motivo() ?? 'Dentro dos limites configurados no momento da geração.', 'full' => true],
            ]" />
        @else
            <x-ui.detail-card>Este registro é antigo e não guardou o valor lido nem os limites configurados no momento da geração.</x-ui.detail-card>
        @endif
    </x-ui.detail-section>
</x-ui.modal-funcional>
