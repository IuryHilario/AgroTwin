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
            <legend class="mb-3">
                <i class="fas fa-flask mr-2"></i> <strong>Informações Insumo</strong>
            </legend>
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 md:col-span-6">
                    <strong>Nome:</strong> {{ $insumo->ds_nome }}
                </div>
                <div class="col-span-12 md:col-span-6">
                    <strong>Tipo:</strong> {{ $insumo->tp_insumo ? $insumo->tp_insumo->label() : 'Não informado' }}
                </div>
                <div class="col-span-12 md:col-span-6">
                    <strong>Fabricante:</strong> {{ $insumo->ds_fabricante ?? 'Não informado' }}
                </div>
                <div class="col-span-12 md:col-span-6">
                    <strong>Unidade:</strong>
                    {{ $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->label() : 'Não informada' }}
                </div>
            </div>
        </fieldset>

        <!-- Estatísticas de Aplicação -->
        @php
            $aplicacoes = $aplicacao->getAplicacoes()->get();
            $totalAplicacoes = $aplicacoes->count();
            $totalQuantidade = $aplicacoes->sum('nu_quantidade_aplicada');
            $totalArea = $aplicacoes->sum('nu_area_aplicada');
            $dosageMedia = $totalArea > 0 ? $totalQuantidade / $totalArea : 0;
        @endphp
        <div class="grid grid-cols-12 gap-4">
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
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12">
                @if($aplicacoes->isEmpty())
                    <div class="card w-full">
                        <div class="p-6 text-center">
                            <p class="text-muted">
                                <i class="fas fa-inbox mb-3 block text-2xl"></i>
                                Nenhuma aplicação registrada ainda.
                            </p>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto rounded-xl shadow-[0_2px_10px_rgba(0,0,0,0.08)]">
                        <table class="w-full border-collapse text-left text-sm">
                            <thead class="bg-subtle">
                                <tr>
                                    <th class="px-4 py-3">Data/Hora</th>
                                    <th class="px-4 py-3">Lavoura</th>
                                    <th class="px-4 py-3">Área (ha)</th>
                                    <th class="px-4 py-3">Quantidade</th>
                                    <th class="px-4 py-3">Dosagem</th>
                                    <th class="px-4 py-3">Método</th>
                                    <th class="px-4 py-3">Responsável</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($aplicacoes as $app)
                                    <tr class="border-t border-subtle hover-surface">
                                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($app->dt_aplicacao)->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-3">{{ $app->lavoura->ds_cultura ?? 'N/A' }}</td>
                                        <td class="px-4 py-3">{{ number_format($app->nu_area_aplicada, 2, ',', '.') }}</td>
                                        <td class="px-4 py-3">{{ number_format($app->nu_quantidade_aplicada, 2, ',', '.') }} {{ $aplicacao->tp_unidade_medida ? $aplicacao->tp_unidade_medida->value : 'UN' }}</td>
                                        <td class="px-4 py-3">{{ number_format($app->nu_dosagem_hectare ?? 0, 2, ',', '.') }} {{ $aplicacao->tp_unidade_medida ? $aplicacao->tp_unidade_medida->value : 'UN' }}/ha</td>
                                        <td class="px-4 py-3">
                                            @if($app->tp_metodo_aplicacao)
                                                @php
                                                    $metodoEnum = \App\Enums\TipoMetodoAplicacao::tryFrom($app->tp_metodo_aplicacao);
                                                @endphp
                                                {{ $metodoEnum?->label() ?? $app->tp_metodo_aplicacao }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">{{ $app->ds_responsavel }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="py-8 text-center text-muted">
            <i class="fas fa-exclamation-triangle mb-2 block text-2xl"></i>
            <p>Insumo não encontrado.</p>
        </div>
    @endif

</x-ui.modal-funcional>
