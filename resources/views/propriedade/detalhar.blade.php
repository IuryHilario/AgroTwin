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
        @if($propriedade->ds_localizacao && $weatherData['success'])
            <x-ui.detail-fields :fields="[
                ['label' => 'Temperatura Atual', 'value' => $weatherData['data']['temperature'] . ' °C'],
                ['label' => 'Sensação Térmica', 'value' => $weatherData['data']['feels_like'] . ' °C'],
                ['label' => 'Umidade', 'value' => $weatherData['data']['humidity'] . ' %'],
                ['label' => 'Descrição', 'value' => ucfirst($weatherData['data']['description'])],
                ['label' => 'Velocidade do Vento', 'value' => $weatherData['data']['wind_speed'] . ' km/h'],
            ]" />
        @else
            <p class="text-sm text-muted">
                <i class="fas fa-exclamation-circle mr-2"></i>
                Cidade indisponível para consultas do tempo.
            </p>
        @endif
    </x-ui.detail-section>
</x-ui.modal-funcional>
