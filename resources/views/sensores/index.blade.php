@extends('layouts.index')

@section('title', 'Sensores - AgroTwin')

@section('page-content')
    <x-ui.section-header
        title="Sensores"
        icon="fas fa-microchip"
    />

    <x-ui.responsive-table-card
        :arTableHead="[
            ['label' => 'Nome', 'key' => 'ds_sensor'],
            ['label' => 'Tipo', 'key' => 'tp_sensor'],
        ]"
        :arValores="$sensores"
        showActions="true"
    />
@endsection

@php
    $fabRoute = route('sensores.inserir');
    $fabText = 'Novo Sensor';
@endphp
