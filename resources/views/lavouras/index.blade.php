@extends('layouts.index')

@section('title', 'Lavouras - AgroTwin')

@section('page-content')

    <x-ui.section-header
        title="Lavouras"
        icon="fas fa-seedling"
    />

    <x-ui.responsive-table-card
        :arTableHead="[
            ['label' => 'Nome', 'key' => 'ds_cultura'],
            ['label' => 'Status', 'key' => 'tp_status'],
            ['label' => 'Data de Plantio', 'key' => 'dt_plantio', 'function' => 'formatDate'],
            ['label' => 'Data de Colheita', 'key' => 'dt_colheita', 'function' => 'formatDate'],
            ['label' => 'Propriedade', 'key' => 'propriedade.ds_nome'],
        ]"
        :arValores="$lavouras"
        showActions="true"
    />

@endsection

@php
    $fabRoute = route('lavouras.inserir');
    $fabText = 'Nova Lavoura';
@endphp
