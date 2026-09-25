@extends('layouts.index')

@section('title', ($title ?? 'Nova Propriedade') . ' - AgroTwin')

@php $emEdicao = $isEdit ?? false; @endphp

@section('page-content')
    <x-ui.section-header
        :buttons="[
            [
                'route' => route('propriedade.index'),
                'text' => 'Voltar',
                'icon' => 'fas fa-arrow-left'
            ],
        ]"
        title="{{ $title ?? 'Nova Propriedade' }}"
        modulo="propriedades"
        etapa="{{ $emEdicao ? 'Edição' : 'Cadastro' }}"
        subtitle="Dados da área, localização no mapa e revisão. As coordenadas alimentam a previsão do tempo e a chance de chuva da propriedade."
    />

    <x-form.form
        action="{{ $action ?? route('propriedade.store') }}"
        method="{{ $method ?? 'POST' }}"
        :acoes="false"
    >
        <x-ui.stepper
            class="col-span-12"
            :etapas="['Propriedade', 'Localidade', 'Finalização']"
            :navegacao-livre="$emEdicao"
            :rotulo-enviar="$emEdicao ? 'Salvar alterações' : 'Cadastrar propriedade'"
        >
            <x-ui.etapa
                :numero="1"
                titulo="Dados da propriedade"
                descricao="Como a área é identificada e o que se sabe do solo."
            >
                <div class="col-span-12">
                    <x-form.input
                        name="ds_nome"
                        label="Nome da propriedade"
                        type="text"
                        required
                        maxlength="255"
                        placeholder="Ex.: Fazenda Boa Vista"
                        :value="old('ds_nome', $propriedade->ds_nome ?? '')"
                        error="{{ $errors->first('ds_nome') }}"
                    />
                </div>

                <div class="col-span-12 md:col-span-6">
                    <x-form.input
                        name="nu_area_hectares"
                        label="Área (ha)"
                        type="number"
                        step="0.01"
                        min="0"
                        required
                        :value="old('nu_area_hectares', $propriedade->nu_area_hectares ?? '')"
                        error="{{ $errors->first('nu_area_hectares') }}"
                    />
                </div>

                <div class="col-span-12 md:col-span-6">
                    <x-form.select
                        name="tp_solo"
                        label="Tipo de solo"
                        :options="\App\Enums\TipoSolo::toSelectArray(true)"
                        :value="old('tp_solo', $propriedade->tp_solo?->value ?? '')"
                        error="{{ $errors->first('tp_solo') }}"
                        ajuda="Opcional. Ajuda a interpretar as leituras dos sensores."
                    />
                </div>
            </x-ui.etapa>

            <x-ui.etapa
                :numero="2"
                titulo="Localização"
                descricao="Busque o município, use sua localização ou clique no mapa. Ajuste o pino para o ponto da lavoura: o centro da cidade pode estar a vários quilômetros e o tempo muda."
            >
                <div class="col-span-12">
                    <x-form.localidade
                        obrigatorio
                        :latitude="$propriedade->nu_latitude ?? null"
                        :longitude="$propriedade->nu_longitude ?? null"
                        :rotulo="$propriedade->ds_localizacao ?? ''"
                        error-latitude="{{ $errors->first('nu_latitude') ?: $errors->first('nu_longitude') }}"
                        error-rotulo="{{ $errors->first('ds_localizacao') }}"
                    />
                </div>
            </x-ui.etapa>

            <x-ui.etapa
                :numero="3"
                titulo="Finalização"
                descricao="Confira os dados antes de salvar. A previsão abaixo usa exatamente o ponto marcado."
            >
                <div class="col-span-12">
                    <x-ui.resumo-etapas />
                </div>

                <div class="col-span-12">
                    <x-ui.previsao-clima />
                </div>
            </x-ui.etapa>
        </x-ui.stepper>
    </x-form.form>
@endsection
