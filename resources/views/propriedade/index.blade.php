@extends('layouts.index')

@section('title', 'Propriedades - AgroTwin')

@section('page-content')

    <x-ui.section-header
        title="Propriedades"
        icon="fas fa-map-marked-alt"
    />

    <x-ui.responsive-table-card
        :arTableHead="[
            ['label' => 'Nome', 'key' => 'ds_nome'],
            ['label' => 'Localização', 'key' => 'ds_localizacao'],
            ['label' => 'Área (ha)', 'key' => 'nu_area_hectares'],
            ['label' => 'Tipo de Solo', 'key' => 'tp_solo'],
        ]"
        :arValores="$propriedade"
        showActions="true"
    />

@endsection

@php
    $fabRoute = route('propriedade.inserir');
    $fabText = 'Nova Propriedade';
@endphp
