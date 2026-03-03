<x-ui.modal-funcional
    modalId="detalharLavoura"
    title="Detalhes da Lavoura"
    icon="fas fa-seedling"
    size="modal-lg"
    :item="$lavoura ?? null"
    resourceName="lavoura"
>
    @php $detalhes = $lavoura->getDetalhesFormatados(); @endphp
    <div class="mdl-grid">
        <div class="mdl-cell mdl-cell--12-col">
            <h6 class="mdl-color-text--grey-600" style="margin-bottom: 16px;">
                <i class="fas fa-info-circle"></i>
                Informações Gerais
            </h6>
        </div>
        <div class="mdl-cell mdl-cell--12-col">
            <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp" style="width: 100%;">
                <tbody>
                    <tr>
                        <td class="mdl-data-table__cell--non-numeric"><strong>Cultura:</strong></td>
                        <td class="mdl-data-table__cell--non-numeric">{{ $detalhes['cultura'] }}</td>
                    </tr>
                    <tr>
                        <td class="mdl-data-table__cell--non-numeric"><strong>Status:</strong></td>
                        <td class="mdl-data-table__cell--non-numeric">
                            @php
                                $statusClass = match($detalhes['status']) {
                                    'Ativo' => 'bg-success',
                                    'Inativo' => 'bg-danger',
                                    'Planejado' => 'bg-info',
                                    'Colhido' => 'bg-secondary',
                                    default => 'bg-primary'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ $detalhes['status'] }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="mdl-data-table__cell--non-numeric"><strong>Data de Plantio:</strong></td>
                        <td class="mdl-data-table__cell--non-numeric">
                            @if($detalhes['dt_plantio'])
                                {{ \Carbon\Carbon::parse($detalhes['dt_plantio'])->format('d/m/Y') }}
                            @else
                                Não informada
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="mdl-data-table__cell--non-numeric"><strong>Data de Colheita:</strong></td>
                        <td class="mdl-data-table__cell--non-numeric">
                            @if($detalhes['dt_colheita'])
                                {{ \Carbon\Carbon::parse($detalhes['dt_colheita'])->format('d/m/Y') }}
                                @php
                                    $today = \Carbon\Carbon::now();
                                    $colheita = \Carbon\Carbon::parse($detalhes['dt_colheita']);
                                    $diasParaColheita = $today->diffInDays($colheita, false);
                                @endphp
                                @if($diasParaColheita < 0)
                                    <span class="badge bg-info ms-2">Colheita passou</span>
                                @elseif($diasParaColheita <= 7)
                                    <span class="badge bg-warning ms-2">Colheita próxima</span>
                                @else
                                    <span class="badge bg-success ms-2">Em desenvolvimento</span>
                                @endif
                            @else
                                Não informada
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="mdl-data-table__cell--non-numeric"><strong>Propriedade:</strong></td>
                        <td class="mdl-data-table__cell--non-numeric">{{ $detalhes['propriedade'] }}</td>
                    </tr>
                    <tr>
                        <td class="mdl-data-table__cell--non-numeric"><strong>Proprietário:</strong></td>
                        <td class="mdl-data-table__cell--non-numeric">{{ $detalhes['proprietario'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mdl-cell mdl-cell--12-col">
            <h6 class="mdl-color-text--grey-600" style="margin-bottom: 16px;">
                <i class="fas fa-sticky-note"></i>
                Observações
            </h6>
            <div class="mdl-card mdl-shadow--2dp" style="width: 100%; min-height: auto;">
                <div class="mdl-card__supporting-text">
                    {!! nl2br(e($detalhes['observacao'])) !!}
                </div>
            </div>
        </div>
    </div>
</x-ui.modal-funcional>
