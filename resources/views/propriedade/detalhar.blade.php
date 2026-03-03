<x-ui.modal-funcional
    modalId="detalharPropriedade"
    title="Detalhes da Propriedade"
    icon="fas fa-home"
    size="modal-lg"
    :item="$propriedade ?? null"
    resourceName="propriedade"
>
    @if($propriedade)
        @php $detalhes = $propriedade->getDetalhesFormatados(); @endphp

        <!-- Informações Básicas -->
        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--12-col">
            <x-ui.table style="width: 100%;">
                <tr>
                    <x-ui.td><strong>Nome:</strong></x-ui.td>
                    <x-ui.td>{{ $detalhes['nome'] }}</x-ui.td>
                </tr>
                <tr>
                    <x-ui.td><strong>Proprietário:</strong></x-ui.td>
                    <x-ui.td>{{ $detalhes['proprietario'] }}</x-ui.td>
                </tr>
                <tr>
                    <x-ui.td><strong>Tipo de Solo:</strong></x-ui.td>
                    <x-ui.td>{{ $detalhes['tp_solo'] }}</x-ui.td>
                </tr>
                <tr>
                    <x-ui.td><strong>Área Total (ha):</strong></x-ui.td>
                    <x-ui.td>{{ $detalhes['area_hectares'] }}</x-ui.td>
                </tr>
            </x-ui.table>
            </div>
        </div>

        <!-- Localização -->
        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--12-col">
                <h6 class="mdl-color-text--grey-600" style="margin-bottom: 16px;">
                    <i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i>
                    Localização
                </h6>
                <div class="mdl-card mdl-shadow--2dp" style="width: 100%; min-height: auto;">
                    <div class="mdl-card__supporting-text">
                        {{ $detalhes['localizacao'] }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--12-col">
                <h6 class="mdl-color-text--grey-600" style="margin-bottom: 16px;">
                    <i class="fas fa-chart-line" style="margin-right: 8px;"></i>
                    Estatísticas
                </h6>
            </div>

            <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
                <div class="mdl-card mdl-shadow--2dp" style="width: 100%; min-height: auto;">
                    <div class="mdl-card__supporting-text" style="text-align: center; padding: 24px;">
                        <i class="fas fa-seedling fa-2x mdl-color-text--green" style="margin-bottom: 16px; display: block;"></i>
                        <h5 style="margin: 8px 0; font-size: 1.5em; font-weight: 500;">{{ $detalhes['total_lavouras'] }}</h5>
                        <small class="mdl-color-text--grey-600">Lavouras Cadastradas</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Condições Meteológicas -->
        <div class="mdl-cell mdl-cell--12-col">
            <h6 class="mdl-color-text--grey-600" style="margin-bottom: 16px;">
                <i class="fas fa-cloud-sun" style="margin-right: 8px;"></i>
                Condições Meteológicas

            </h6>
            <div class="mdl-cell mdl-cell--12-col">
                <x-ui.table style="width: 100%;">
                        @if($propriedade->ds_localizacao && $weatherData['success'])
                            <tr>
                                <x-ui.td>
                                    <i class="fas fa-thermometer-half" style="margin-right: 8px;"></i>
                                    <strong>Temperatura Atual:</strong>
                                </x-ui.td>
                                <x-ui.td>{{ $weatherData['data']['temperature'] }} °C</x-ui.td>
                            </tr>
                            <tr>
                                <x-ui.td>
                                    <i class="fas fa-temperature-low" style="margin-right: 8px;"></i>
                                    <strong>Sensação Térmica:</strong>
                                </x-ui.td>
                                <x-ui.td>{{ $weatherData['data']['feels_like'] }} °C</x-ui.td>
                            </tr>
                            <tr>
                                <x-ui.td>
                                    <i class="fas fa-tint" style="margin-right: 8px;"></i>
                                    <strong>Umidade:</strong>
                                </x-ui.td>
                                <x-ui.td>{{ $weatherData['data']['humidity'] }} %</x-ui.td>
                            </tr>
                            <tr>
                                <x-ui.td>
                                    <i class="fas fa-cloud" style="margin-right: 8px;"></i>
                                    <strong>Descrição:</strong>
                                </x-ui.td>
                                <x-ui.td>{{ ucfirst($weatherData['data']['description']) }}</x-ui.td>
                            </tr>
                            <tr>
                                <x-ui.td>
                                    <i class="fas fa-wind" style="margin-right: 8px;"></i>
                                    <strong>Velocidade do Vento:</strong>
                                </x-ui.td>
                                <x-ui.td>{{ $weatherData['data']['wind_speed'] }} km/h</x-ui.td>
                            </tr>
                        @else
                            <tr>
                                <x-ui.td colspan="2">
                                    <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
                                    Cidade indisponível para consultas do tempo.
                                </x-ui.td>
                            </tr>
                        @endif
                </x-ui.table>
            </div>
        </div>


    @else
        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--12-col" style="text-align: center; padding: 40px;">
                <i class="fas fa-exclamation-triangle fa-2x mdl-color-text--amber" style="margin-bottom: 16px; display: block;"></i>
                <p class="mdl-color-text--grey-600">Propriedade não encontrada.</p>
            </div>
        </div>
    @endif
</x-ui.modal-funcional>
