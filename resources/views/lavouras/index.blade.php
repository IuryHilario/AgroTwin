@extends('layouts.index')

@section('title', 'Lavouras - AgroTwin')

@php
    $fabRoute = route('lavouras.inserir');
    $fabText = 'Nova Lavoura';
@endphp

@section('page-content')

    <x-ui.section-header
        modulo="lavouras"
        title="Lavouras"
        subtitle="Cada lavoura tem seus próprios sensores, faixas ideais e irrigação."
        :stats="$stats"
        :acao="['route' => $fabRoute, 'text' => $fabText]"
    />

    <x-ui.responsive-table-card
        :arTableHead="[
            ['label' => 'Cultura', 'key' => 'ds_cultura', 'destaque' => true],
            ['label' => 'Status', 'key' => 'tp_status', 'tipo' => 'status'],
            ['label' => 'Irrigação', 'key' => 'fl_irrigacao_ativa', 'tipo' => 'booleano', 'rotuloSim' => 'Irrigando', 'tomSim' => 'info', 'rotuloNao' => 'Parada', 'tomNao' => 'neutro'],
            ['label' => 'Plantio', 'key' => 'dt_plantio', 'tipo' => 'data'],
            ['label' => 'Colheita', 'key' => 'dt_colheita', 'tipo' => 'data'],
            ['label' => 'Propriedade', 'key' => 'propriedade.ds_nome'],
        ]"
        :arValores="$lavouras"
        showActions="true"
        :busca="$busca"
        buscaPlaceholder="Buscar por cultura"
        vazioIcone="fa-seedling"
        vazioTitulo="Nenhuma lavoura cadastrada"
        vazioTexto="Cadastre uma lavoura para associar sensores, definir as faixas ideais do solo e ligar a irrigação automática."
        :vazioRota="route('lavouras.inserir')"
        vazioAcao="Cadastrar lavoura"
    />

@endsection
