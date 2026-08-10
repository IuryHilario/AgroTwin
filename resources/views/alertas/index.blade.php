@extends('layouts.app')

@section('title', 'Alertas - AgroTwin')

@section('content')

    <x-ui.section-header
        title="Alertas"
        icon="fas fa-bell"
    />

    <x-ui.responsive-table-card
        :arTableHead="[
            ['label' => 'Mensagem', 'key' => 'ds_mensagem'],
            ['label' => 'Severidade', 'key' => 'tp_severidade', 'function' => 'formatTitle'],
            ['label' => 'Lavoura', 'key' => 'lavoura.ds_cultura'],
            ['label' => 'Data', 'key' => 'dt_alerta', 'function' => 'formatDateTime'],
        ]"
        :arValores="$alertas"
        showActions="true"
    />

@endsection
