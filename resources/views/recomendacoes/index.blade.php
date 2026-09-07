@extends('layouts.app')

@section('title', 'Recomendações - AgroTwin')

@section('content')

    <x-ui.section-header
        title="Recomendações"
        icon="fas fa-brain"
    />

    <x-ui.responsive-table-card
        :arTableHead="[
            ['label' => 'Recomendação', 'key' => 'ds_recomendacao'],
            ['label' => 'Sensor', 'key' => 'tp_sensor'],
            ['label' => 'Lavoura', 'key' => 'lavoura.ds_cultura'],
            ['label' => 'Data', 'key' => 'dt_recomendacao', 'function' => 'formatDateTime'],
        ]"
        :arValores="$recomendacoes"
        showActions="true"
    />

@endsection
