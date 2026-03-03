@extends('layouts.index')

@section('title', 'Insumos - AgroTwin')

@section('page-content')

    <x-ui.section-header
        title="Insumos"
        icon="fas fa-flask"
    />

    <x-ui.responsive-table-card
        :arTableHead="[
            ['label' => 'Nome', 'key' => 'ds_nome'],
            ['label' => 'Tipo', 'key' => 'tp_insumo'],
            ['label' => 'Unidade Medida', 'key' => 'tp_unidade_medida'],
            ['label' => 'Data Validade', 'key' => 'dt_validade', 'function' => 'formatDate'],
        ]"
        :arValores="$insumos"
        showActions={{ true }}
    />

@endsection

@php
    $fabRoute = route('insumos.inserir');
    $fabText = 'Novo Insumo';
@endphp
