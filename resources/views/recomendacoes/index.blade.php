@extends('layouts.index')

@section('title', 'Recomendações - AgroTwin')

@section('page-content')

    <x-ui.section-header
        modulo="recomendacoes"
        title="Recomendações"
        subtitle="Sugestões geradas pelas regras condicionais a partir das leituras dos sensores."
        :stats="$stats"
    />

    <x-ui.responsive-table-card
        :arTableHead="[
            ['label' => 'Recomendação', 'key' => 'ds_recomendacao', 'destaque' => true],
            ['label' => 'Sensor', 'key' => 'tp_sensor'],
            ['label' => 'Lavoura', 'key' => 'lavoura.ds_cultura'],
            ['label' => 'Data', 'key' => 'dt_recomendacao', 'tipo' => 'data_hora'],
        ]"
        :arValores="$recomendacoes"
        showActions="true"
        :busca="$busca"
        buscaPlaceholder="Buscar no texto da recomendação"
        vazioIcone="fa-lightbulb"
        vazioTitulo="Nenhuma recomendação ainda"
        vazioTexto="As recomendações surgem conforme os sensores reportam leituras. Configure as faixas ideais das lavouras para o sistema ter com o que comparar."
    />

@endsection
