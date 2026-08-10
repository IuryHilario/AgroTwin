@php
use \Carbon\Carbon;
@endphp

<x-ui.modal-funcional
    modalId="detalharLavoura"
    title="Detalhes da Lavoura"
    icon="fas fa-seedling"
    size="modal-lg"
    :item="$lavoura ?? null"
    resourceName="lavoura"
>
    @php $detalhes = $lavoura->getDetalhesFormatados(); @endphp

    <x-ui.detail-section title="Informações Gerais" icon="fas fa-info-circle">
        <x-ui.detail-fields :fields="[
            ['label' => 'Cultura', 'value' => $detalhes['cultura']],
            ['label' => 'Status', 'value' => $detalhes['status']],
            ['label' => 'Data de Plantio', 'value' => $detalhes['dt_plantio'] ? Carbon::parse($detalhes['dt_plantio'])->format('d/m/Y') : 'Não informada'],
            ['label' => 'Data de Colheita', 'value' => $detalhes['dt_colheita'] ? Carbon::parse($detalhes['dt_colheita'])->format('d/m/Y') : 'Não informada'],
            ['label' => 'Propriedade', 'value' => $detalhes['propriedade']],
            ['label' => 'Proprietário', 'value' => $detalhes['proprietario']],
        ]" />
    </x-ui.detail-section>

    <x-ui.detail-section title="Observações" icon="fas fa-sticky-note">
        <x-ui.detail-card>{{ $detalhes['observacao'] ?: 'Nenhuma observação registrada.' }}</x-ui.detail-card>
    </x-ui.detail-section>
</x-ui.modal-funcional>
