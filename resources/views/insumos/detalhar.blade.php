<x-ui.modal-funcional
    modalId="detalharInsumo"
    title="Detalhes do Insumo"
    icon="fas fa-info-circle"
    size="modal-lg"
    :item="$insumo ?? null"
    resourceName="insumo"

>
    @php $detalhes = $insumo->getDetalhesFormatados(); @endphp
    <div class="mdl-grid">
        <div class="mdl-cell mdl-cell--12-col">
            <h6 class="text-muted mb-3">
                <i class="fas fa-info-circle me-2"></i>
                Informações Principais
            </h6>

            <x-ui.table>
                <tr>
                    <x-ui.td><strong>Nome</strong></x-ui.td>
                    <x-ui.td>{{ $detalhes['nome'] }}</x-ui.td>
                </tr>
                <tr>
                    <x-ui.td><strong>Tipo</strong></x-ui.td>
                    <x-ui.td>{{ $detalhes['tipo'] }}</x-ui.td>
                </tr>
                <tr>
                    <x-ui.td><strong>Fabricante</strong></x-ui.td>
                    <x-ui.td>{{ $detalhes['fabricante'] }}</x-ui.td>
                </tr>
                <tr>
                    <x-ui.td><strong>Unidade de Medida</strong></x-ui.td>
                    <x-ui.td>{{ $detalhes['unidade_medida'] }}</x-ui.td>
                </tr>
                <tr>
                    <x-ui.td><strong>Data Validade</strong></x-ui.td>
                    <x-ui.td>
                        @if($detalhes['data_validade'])
                            {{ \Carbon\Carbon::parse($detalhes['data_validade'])->format('d/m/Y') }}
                            @php
                                $today = \Carbon\Carbon::now();
                                $validade = \Carbon\Carbon::parse($detalhes['data_validade']);
                                $diasRestantes = (int)$today->diffInDays($validade, false);
                            @endphp
                            @if($diasRestantes < 0)
                                <span>Vencido</span>

                            @elseif($diasRestantes <= 30)
                                <span>Vence em {{ $diasRestantes }} dias</span>

                            @else
                                <span>Válido</span>

                            @endif
                        @else
                            <span>Não informada</span>

                        @endif
                    </x-ui.td>
                </tr>
                <tr>
                    <x-ui.td><strong>Usuário Responsável</strong></x-ui.td>
                    <x-ui.td>{{ $detalhes['usuario'] }}</x-ui.td>
                </tr>
            </x-ui.table>
        </div>

        <!-- Composição -->
        <div class="mdl-cell mdl-cell--12-col">
            <h6 class="text-muted mb-3">
                <i class="fas fa-atom me-2"></i>
                Composição
            </h6>
            <div class="mdl-card mdl-shadow--2dp" style="width: 100%;">
                <div class="mdl-card__supporting-text">
                    {!! nl2br(e($detalhes['composicao'])) !!}
                </div>
            </div>
        </div>
    </div>
</x-ui.modal-funcional>
