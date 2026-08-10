@extends('layouts.app')

@section('title', ($title ?? 'Nova Propriedade') . ' - AgroTwin')

@section('content')
    <x-ui.section-header
        :buttons="[
            [
                'route' => route('propriedade.index'),
                'text' => 'Voltar propriedade',
                'icon' => 'fas fa-arrow-left'
            ],
        ]"
        title="{{ $title ?? 'Nova Propriedade' }}"
        icon="{{ $icon ?? 'fas fa-plus-circle' }}"
    />

    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12">
            <x-form.form
                action="{{ $action ?? route('propriedade.store') }}"
                method="{{ $method ?? 'POST' }}"
                title="Informações da Propriedade"
            >

                <div class="col-span-12">
                    <x-form.input
                        name="ds_nome"
                        label="Nome da Propriedade"
                        type="text"
                        required
                        :value="old('ds_nome', $propriedade->ds_nome ?? '')"
                        error="{{ $errors->first('ds_nome') }}"
                    />
                </div>

                <div class="col-span-12 md:col-span-6">
                    <x-form.input
                        name="nu_area_hectares"
                        label="Área da Propriedade (ha)"
                        type="number"
                        step="0.01"
                        required
                        :value="old('nu_area_hectares', $propriedade->nu_area_hectares ?? '')"
                        error="{{ $errors->first('nu_area_hectares') }}"
                    />
                </div>

                <div class="col-span-12 md:col-span-6">
                    <x-form.select
                        name="tp_solo"
                        label="Tipo de Solo"
                        :options="\App\Enums\TipoSolo::toSelectArray(true)"
                        :value="old('tp_solo', $propriedade->tp_solo?->value ?? '')"
                        error="{{ $errors->first('tp_solo') }}"
                    />
                </div>

                <div class="col-span-12">
                    <x-form.input
                        name="ds_localizacao"
                        label="Endereço"
                        type="text"
                        :value="old('ds_localizacao', $propriedade->ds_localizacao ?? '')"
                        error="{{ $errors->first('ds_localizacao') }}"
                    />
                </div>
            </x-form.form>
        </div>
    </div>
@endsection
