@extends('layouts.index')

@section('title', 'Propriedades - AgroTwin')

@php
    $fabRoute = route('propriedade.inserir');
    $fabText = 'Nova Propriedade';
@endphp

@section('page-content')

    <x-ui.section-header
        modulo="propriedades"
        title="Propriedades"
        subtitle="As áreas que você administra. Cada propriedade agrupa suas lavouras e sensores."
        :stats="$stats"
        :acao="['route' => $fabRoute, 'text' => $fabText]"
    />

    <x-ui.responsive-table-card
        :arTableHead="[
            ['label' => 'Nome', 'key' => 'ds_nome', 'destaque' => true],
            ['label' => 'Localização', 'key' => 'ds_localizacao'],
            ['label' => 'Área', 'key' => 'nu_area_hectares', 'tipo' => 'numero', 'sufixo' => 'ha'],
            ['label' => 'Tipo de Solo', 'key' => 'tp_solo'],
            ['label' => 'Status', 'key' => 'fl_inativo', 'tipo' => 'booleano', 'rotuloSim' => 'Inativa', 'rotuloNao' => 'Ativa'],
        ]"
        :arValores="$propriedade"
        showActions="true"
        :busca="$busca"
        buscaPlaceholder="Buscar por nome ou localização"
        vazioIcone="fa-map-marked-alt"
        vazioTitulo="Nenhuma propriedade cadastrada"
        vazioTexto="A propriedade é o ponto de partida do AgroTwin: é dentro dela que você cadastra lavouras e sensores."
        :vazioRota="route('propriedade.inserir')"
        vazioAcao="Cadastrar propriedade"
    />

@endsection
