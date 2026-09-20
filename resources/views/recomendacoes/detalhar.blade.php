<x-ui.modal-funcional
    modalId="detalharRecomendacao"
    title="Detalhes da Recomendação"
    icon="fas fa-brain"
    size="modal-lg"
    :item="$recomendacao ?? null"
    resourceName="recomendação"
>
    @php
        $unidade = $recomendacao->tp_sensor?->unidade() ?? '';
    @endphp

    <x-ui.detail-section title="Informações Principais" icon="fas fa-info-circle">
        <x-ui.detail-fields :fields="[
            ['label' => 'Lavoura', 'value' => $recomendacao->lavoura?->ds_cultura],
            ['label' => 'Sensor', 'value' => $recomendacao->tp_sensor?->label()],
            ['label' => 'Data', 'value' => $recomendacao->dt_recomendacao->format('d/m/Y H:i')],
        ]" />
    </x-ui.detail-section>

    <x-ui.detail-section title="Recomendação" icon="fas fa-lightbulb">
        <x-ui.detail-card>{{ $recomendacao->ds_recomendacao }}</x-ui.detail-card>
    </x-ui.detail-section>

    <x-ui.detail-section title="Por que essa recomendação foi gerada" icon="fas fa-circle-question">
        @if($recomendacao->nu_valor_leitura !== null)
            <x-ui.detail-fields :fields="[
                ['label' => 'Valor Lido', 'value' => $recomendacao->nu_valor_leitura . $unidade],
                ['label' => 'Limite Mínimo', 'value' => $recomendacao->nu_limite_min !== null ? $recomendacao->nu_limite_min . $unidade : 'Não configurado'],
                ['label' => 'Limite Máximo', 'value' => $recomendacao->nu_limite_max !== null ? $recomendacao->nu_limite_max . $unidade : 'Não configurado'],
                ['label' => 'Motivo', 'value' => $recomendacao->motivo() ?? 'Dentro dos limites configurados no momento da geração.', 'full' => true],
            ]" />
        @else
            <x-ui.detail-card>Este registro é antigo e não guardou o valor lido nem os limites configurados no momento da geração.</x-ui.detail-card>
        @endif
    </x-ui.detail-section>
</x-ui.modal-funcional>
