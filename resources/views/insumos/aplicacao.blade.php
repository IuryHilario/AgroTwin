@php
    $insumo = $insumo ?? $aplicacao ?? null;
    $aplicacao = $aplicacao ?? $insumo;
@endphp

<x-ui.modal-funcional
    modalId="aplicacaoInsumo"
    title="Aplicação de Insumo"
    icon="fas fa-spray-can"
    size="modal-xl"
    :item="$insumo"
    resourceName="insumo"
    :additionalButtons="[
        [
            'tag' => 'button',
            'text' => 'Nova Aplicação',
            'class' => 'btn-warning',
            'icon' => 'fas fa-plus',
            'type' => 'button',
            'data-action' => 'nova-aplicacao',
            'data-url' => $insumo ? route('insumos.aplicacao.create', $insumo->id_insumo) : '#'
        ]
    ]"
>

    @if($insumo)
        <fieldset>
            <legend>
                <i class="fas fa-flask me-2"></i> <strong>Informações Insumo</strong>
            </legend>
            <div class="mdl-grid">
                <div class="mdl-cell mdl-cell--6-col">
                    <strong>Nome:</strong> {{ $insumo->ds_nome }}
                </div>
                <div class="mdl-cell mdl-cell--6-col">
                    <strong>Tipo:</strong> {{ $insumo->tp_insumo ? $insumo->tp_insumo->label() : 'Não informado' }}
                </div>
                <div class="mdl-cell mdl-cell--6-col">
                    <strong>Fabricante:</strong> {{ $insumo->ds_fabricante ?? 'Não informado' }}
                </div>
                <div class="mdl-cell mdl-cell--6-col">
                    <strong>Unidade:</strong>
                    {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->label() : 'Não informada' }}
                </div>
            </div>
        </fieldset>

        <!-- Estatísticas de Aplicação -->
        @php
            $aplicacoes = $aplicacao->relationLoaded('getAplicacoes')
                ? $aplicacao->getAplicacoes
                : collect();
            $totalAplicacoes = $aplicacoes->count();
            $totalQuantidade = $aplicacoes->sum('nu_quantidade_aplicada');
            $totalArea = $aplicacoes->sum('nu_area_aplicada');
            $dosageMedia = $totalArea > 0 ? $totalQuantidade / $totalArea : 0;
        @endphp
        <div class="mdl-grid">
            <x-extras.card
                title="Total Aplicações"
                icon="fa-calendar-check"
                color="red"
                valor="{{ $totalAplicacoes }}"
            />
            <x-extras.card
                title="Quantidade Aplicada"
                icon="fa-weight-hanging"
                color="#4CAF50"
                valor="{{ number_format($totalQuantidade, 2, ',', '.') }} {{ $aplicacao->tp_unidade_medida ? $aplicacao->tp_unidade_medida->value : 'UN' }}"
            />
            <x-extras.card
                title="Área Coberta"
                icon="fa-map-marked-alt"
                color="blue"
                valor="{{ number_format($totalArea, 2, ',', '.') }} ha"
            />
            <x-extras.card
                title="Dosagem Média"
                icon="fa-tachometer-alt"
                color="orange"
                valor="{{ number_format($dosageMedia, 2, ',', '.') }} {{ $aplicacao->tp_unidade_medida ? $aplicacao->tp_unidade_medida->value : 'UN' }}/ha"
            />
        </div>


        <!-- Aplicações Recentes -->
        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--12-col">
                @if($aplicacoes->isEmpty())
                    <div class="mdl-card mdl-shadow--2dp" style="width: 100%;">
                        <div class="mdl-card__supporting-text" style="text-align: center;">
                            <p class="text-muted">
                                <i class="fas fa-inbox fa-2x mb-3"></i><br>
                                Nenhuma aplicação registrada ainda.
                            </p>
                        </div>
                    </div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Data/Hora</th>
                                <th>Lavoura</th>
                                <th>Área (ha)</th>
                                <th>Quantidade</th>
                                <th>Dosagem</th>
                                <th>Método</th>
                                <th>Responsável</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($aplicacoes as $app)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($app->dt_aplicacao)->format('d/m/Y H:i') }}</td>
                                    <td>{{ $app->lavoura->ds_cultura ?? 'N/A' }}</td>
                                    <td>{{ number_format($app->nu_area_aplicada, 2, ',', '.') }}</td>
                                    <td>{{ number_format($app->nu_quantidade_aplicada, 2, ',', '.') }} {{ $aplicacao->tp_unidade_medida ? $aplicacao->tp_unidade_medida->value : 'UN' }}</td>
                                    <td>{{ number_format($app->nu_dosagem_hectare ?? 0, 2, ',', '.') }} {{ $aplicacao->tp_unidade_medida ? $aplicacao->tp_unidade_medida->value : 'UN' }}/ha</td>
                                    <td>
                                        @if($app->tp_metodo_aplicacao)
                                            @php
                                                $metodoEnum = \App\Enums\TipoMetodoAplicacao::tryFrom($app->tp_metodo_aplicacao);
                                            @endphp
                                            {{ $metodoEnum?->label() ?? $app->tp_metodo_aplicacao }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $app->ds_responsavel }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    @else
        <div class="text-center text-muted py-4">
            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
            <p>Insumo não encontrado.</p>
        </div>
    @endif

</x-ui.modal-funcional>
