<x-ui.modal-funcional
    modalId="detalharSensor"
    title="Detalhes do Sensor"
    icon="fas fa-microchip"
    size="modal-lg"
    :item="$sensore ?? null"
    resourceName="sensor"
>
    @php $detalhes = $sensore->getDetalhesFormatados(); @endphp

    <x-ui.detail-section title="Informações do Sensor" icon="fas fa-info-circle">
        <x-ui.detail-fields :fields="[
            ['label' => 'Nome', 'value' => $detalhes['nome']],
            ['label' => 'Tipo', 'value' => $detalhes['tipo']],
            ['label' => 'Unidade de Medida', 'value' => $detalhes['unidade'] ?: '-'],
            ['label' => 'Status', 'value' => $detalhes['status']],
            ['label' => 'Propriedade', 'value' => $detalhes['propriedade']],
            ['label' => 'Lavoura', 'value' => $detalhes['lavoura']],
        ]" />
    </x-ui.detail-section>

    <x-ui.detail-section title="Integração com o Dispositivo (ESP32)" icon="fas fa-satellite-dish">
        <x-ui.detail-fields :fields="[
            ['label' => 'URL de Envio (POST)', 'value' => url('/api/sensores/' . $sensore->id_sensor . '/leituras'), 'full' => true],
            ['label' => 'Token (Authorization: Bearer)', 'value' => $detalhes['token'], 'full' => true],
        ]" />
    </x-ui.detail-section>
</x-ui.modal-funcional>
