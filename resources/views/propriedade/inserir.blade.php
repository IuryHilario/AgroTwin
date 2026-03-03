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

    <div class="mdl-grid">
        <div class="mdl-cell mdl-cell--12-col">
            <x-form.form
                action="{{ route('propriedade.store') }}"
                method="POST"
                class="mdl-grid"
                title="Informações da Propriedade"
            >

                <div class="mdl-cell mdl-cell--12-col">
                    <x-form.input
                        name="ds_nome"
                        label="Nome da Propriedade"
                        type="text"
                        required
                        :value="old('ds_nome')"
                        error="{{ $errors->first('ds_nome') }}"
                    />
                </div>

                <div class="mdl-cell mdl-cell--6-col">
                    <x-form.input
                        name="nu_area_hectares"
                        label="Área da Propriedade (ha)"
                        type="number"
                        step="0.01"
                        required
                        :value="old('nu_area_hectares')"
                        error="{{ $errors->first('nu_area_hectares') }}"
                    />
                </div>

                <div class="mdl-cell mdl-cell--6-col">
                    <x-form.select
                        name="tp_solo"
                        label="Tipo de Solo"
                        :options="\App\Enums\TipoSolo::toSelectArray(true)"
                        :value="old('tp_solo')"
                        error="{{ $errors->first('tp_solo') }}"
                    />
                </div>

                <div class="mdl-cell mdl-cell--12-col">
                    <x-form.input
                        name="ds_localizacao"
                        label="Endereço"
                        type="text"
                        :value="old('ds_localizacao')"
                        error="{{ $errors->first('ds_localizacao') }}"
                    />
                </div>


            </x-form.form>
        </div>
    </div>
@endsection


