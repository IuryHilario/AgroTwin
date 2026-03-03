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

            <!-- Tabela com estilo Material Design Lite -->
            <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp" style="width: 100%;">
                <tbody>
                    <tr>
                        <td class="mdl-data-table__cell--non-numeric"><strong>Nome</strong></td>
                        <td class="mdl-data-table__cell--non-numeric">{{ $detalhes['nome'] }}</td>
                    </tr>
                    <tr>
                        <td class="mdl-data-table__cell--non-numeric"><strong>Tipo</strong></td>
                        <td class="mdl-data-table__cell--non-numeric">
                            <span class="mdl-chip">
                                <span class="mdl-chip__text">{{ $detalhes['tipo'] }}</span>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="mdl-data-table__cell--non-numeric"><strong>Fabricante</strong></td>
                        <td class="mdl-data-table__cell--non-numeric">{{ $detalhes['fabricante'] }}</td>
                    </tr>
                    <tr>
                        <td class="mdl-data-table__cell--non-numeric"><strong>Unidade de Medida</strong></td>
                        <td class="mdl-data-table__cell--non-numeric">
                            <span class="mdl-chip">
                                <span class="mdl-chip__text">{{ $detalhes['unidade_medida'] }}</span>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="mdl-data-table__cell--non-numeric"><strong>Data de Validade</strong></td>
                        <td class="mdl-data-table__cell--non-numeric">
                            @if($detalhes['data_validade'])
                                {{ \Carbon\Carbon::parse($detalhes['data_validade'])->format('d/m/Y') }}
                                @php
                                    $today = \Carbon\Carbon::now();
                                    $validade = \Carbon\Carbon::parse($detalhes['data_validade']);
                                    $diasRestantes = $today->diffInDays($validade, false);
                                @endphp
                            @else
                                <span class="mdl-chip">
                                    <span class="mdl-chip__text">Não informada</span>
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="mdl-data-table__cell--non-numeric"><strong>Usuário Responsável</strong>
                        </td>
                        <td class="mdl-data-table__cell--non-numeric">{{ $detalhes['usuario'] }}</td>
                    </tr>
                </tbody>
            </table>
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
