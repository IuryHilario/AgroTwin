@extends('layouts.index')

@section('title', 'Sensores - AgroTwin')

@php
    $fabRoute = route('sensores.inserir');
    $fabText = 'Novo Sensor';
@endphp

@section('page-content')
    <x-ui.section-header
        modulo="sensores"
        title="Sensores"
        subtitle="Dispositivos que enviam as leituras do solo para o sistema."
        :stats="$stats"
        :acao="['route' => $fabRoute, 'text' => $fabText]"
    />

    <x-ui.responsive-table-card
        :arTableHead="[
            ['label' => 'Nome', 'key' => 'ds_nome', 'destaque' => true],
            ['label' => 'Tipo', 'key' => 'tp_sensor'],
            ['label' => 'Lavoura', 'key' => 'lavoura.ds_cultura'],
            ['label' => 'Última leitura', 'key' => 'ultimaLeitura.dt_leitura', 'tipo' => 'conexao'],
            ['label' => 'Status', 'key' => 'ds_status', 'tipo' => 'status'],
        ]"
        :arValores="$sensores"
        showActions="true"
        :busca="$busca"
        buscaPlaceholder="Buscar por nome"
        vazioIcone="fa-microchip"
        vazioTitulo="Nenhum sensor cadastrado"
        vazioTexto="Ao cadastrar um sensor o sistema gera o token que o ESP32 usa para enviar as leituras."
        :vazioRota="route('sensores.inserir')"
        vazioAcao="Cadastrar sensor"
    />
@endsection
