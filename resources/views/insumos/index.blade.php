@extends('layouts.index')

@section('title', 'Insumos - AgroTwin')

@php
    $fabRoute = route('insumos.inserir');
    $fabText = 'Novo Insumo';
@endphp

@section('page-content')

    <x-ui.section-header
        modulo="insumos"
        title="Insumos"
        subtitle="Fertilizantes, defensivos e sementes, com controle de estoque e registro de aplicação."
        :stats="$stats"
        :acao="['route' => $fabRoute, 'text' => $fabText]"
    />

    <x-ui.responsive-table-card
        :arTableHead="[
            ['label' => 'Nome', 'key' => 'ds_nome', 'destaque' => true],
            ['label' => 'Tipo', 'key' => 'tp_insumo'],
            ['label' => 'Estoque', 'key' => 'estoque_atual', 'tipo' => 'estoque', 'casas' => 0],
            ['label' => 'Unidade', 'key' => 'tp_unidade_medida'],
            ['label' => 'Validade', 'key' => 'dt_validade', 'tipo' => 'validade'],
        ]"
        :arValores="$insumos"
        showActions="true"
        :busca="$busca"
        buscaPlaceholder="Buscar por nome ou fabricante"
        vazioIcone="fa-flask"
        vazioTitulo="Nenhum insumo cadastrado"
        vazioTexto="Cadastre um insumo para controlar entradas e saídas de estoque e registrar as aplicações na lavoura."
        :vazioRota="route('insumos.inserir')"
        vazioAcao="Cadastrar insumo"
    />

@endsection
