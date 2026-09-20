<x-ui.modal-funcional
    modalId="detalharPropriedade"
    title="Detalhes da Propriedade"
    icon="fas fa-home"
    size="modal-lg"
    :item="$propriedade ?? null"
    resourceName="propriedade"
>
    @php $detalhes = $propriedade->getDetalhesFormatados(); @endphp

    <x-ui.detail-section title="Informações Básicas" icon="fas fa-info-circle">
        <x-ui.detail-fields :fields="[
            ['label' => 'Nome', 'value' => $detalhes['nome']],
            ['label' => 'Proprietário', 'value' => $detalhes['proprietario']],
            ['label' => 'Tipo de Solo', 'value' => $detalhes['tp_solo']],
            ['label' => 'Área Total (ha)', 'value' => $detalhes['area_hectares']],
            ['label' => 'Lavouras Cadastradas', 'value' => $detalhes['total_lavouras']],
            ['label' => 'Status', 'value' => $detalhes['status']],
        ]" />
    </x-ui.detail-section>

    <x-ui.detail-section title="Localização" icon="fas fa-map-marker-alt">
        <x-ui.detail-card>{{ $detalhes['localizacao'] ?: 'Não informada.' }}</x-ui.detail-card>
    </x-ui.detail-section>

    <x-ui.detail-section title="Condições Meteorológicas" icon="fas fa-cloud-sun">
        @if($clima)
            <x-ui.detail-fields :fields="[
                ['label' => 'Temperatura Atual', 'value' => $clima['temperatura'] . ' °C'],
                ['label' => 'Sensação Térmica', 'value' => $clima['sensacao'] . ' °C'],
                ['label' => 'Umidade do Ar', 'value' => $clima['umidade'] . ' %'],
                ['label' => 'Condição', 'value' => $clima['descricao']],
                ['label' => 'Vento', 'value' => $clima['vento_kmh'] . ' km/h'],
            ]" />
        @else
            <p class="text-sm text-muted">
                <i class="fas fa-circle-info mr-2"></i>
                Clima indisponível — confira se a localização da propriedade tem o nome de uma cidade.
            </p>
        @endif
    </x-ui.detail-section>
</x-ui.modal-funcional>
