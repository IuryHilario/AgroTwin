@extends('layouts.index')

@section('title', 'Alertas - AgroTwin')

@section('page-content')

    <x-ui.section-header
        modulo="alertas"
        title="Alertas"
        subtitle="Registrados quando um parâmetro do solo sai da faixa configurada para a lavoura."
        :stats="$stats"
    />

    <x-ui.responsive-table-card
        :arTableHead="[
            ['label' => 'Situação', 'key' => 'fl_lida', 'tipo' => 'booleano', 'rotuloSim' => 'Lido', 'tomSim' => 'neutro', 'rotuloNao' => 'Não lido', 'tomNao' => 'info'],
            ['label' => 'Mensagem', 'key' => 'ds_mensagem', 'destaque' => true],
            ['label' => 'Severidade', 'key' => 'tp_severidade', 'tipo' => 'status'],
            ['label' => 'Lavoura', 'key' => 'lavoura.ds_cultura'],
            ['label' => 'Data', 'key' => 'dt_alerta', 'tipo' => 'data_hora'],
        ]"
        :arValores="$alertas"
        showActions="true"
        vazioIcone="fa-circle-check"
        vazioTitulo="Nenhum alerta por aqui"
        vazioTexto="Nada saiu da faixa ideal nesta seleção. Os alertas aparecem sozinhos quando uma leitura ultrapassa os limites da lavoura."
    >
        <x-slot:filtros>
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <select name="situacao" onchange="this.form.submit()" aria-label="Situação" class="form-control !w-auto !py-1.5 text-sm">
                    @foreach (['nao_lidos' => 'Não lidos', 'lidos' => 'Lidos', 'todos' => 'Todos'] as $chave => $rotulo)
                        <option value="{{ $chave }}" @selected($filtros['situacao'] === $chave)>{{ $rotulo }}</option>
                    @endforeach
                </select>

                <select name="severidade" onchange="this.form.submit()" aria-label="Severidade" class="form-control !w-auto !py-1.5 text-sm">
                    <option value="">Todas as severidades</option>
                    @foreach (['critical' => 'Crítico', 'warning' => 'Atenção', 'info' => 'Informativo'] as $chave => $rotulo)
                        <option value="{{ $chave }}" @selected($filtros['severidade'] === $chave)>{{ $rotulo }}</option>
                    @endforeach
                </select>

                @if ($lavouras->isNotEmpty())
                    <select name="lavoura" onchange="this.form.submit()" aria-label="Lavoura" class="form-control !w-auto !py-1.5 text-sm">
                        <option value="">Todas as lavouras</option>
                        @foreach ($lavouras as $lavoura)
                            <option value="{{ $lavoura->id_lavoura }}" @selected((string) $filtros['lavoura'] === (string) $lavoura->id_lavoura)>
                                {{ $lavoura->ds_cultura }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </form>

            @if ($stats[2]['valor'] > 0)
                <button type="button" class="btn btn-secondary !py-1.5 text-sm"
                        data-action="marcar-todos-lidos"
                        data-url="{{ route('alertas.marcarTodosLidos') }}">
                    <i class="fas fa-check-double"></i>
                    Marcar todos como lidos
                </button>
            @endif
        </x-slot:filtros>
    </x-ui.responsive-table-card>

@endsection
