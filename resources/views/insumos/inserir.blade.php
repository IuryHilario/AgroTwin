@extends('layouts.app')

@section('title', 'Insumos - AgroTwin')

@section('content')

    <x-ui.section-header
        :buttons="[
            [
                'route' => route('insumos.index'),
                'text' => 'Voltar Insumos',
                'icon' => 'fas fa-arrow-left'
            ],
        ]"
        title="Novo Insumo"
        icon="fas fa-plus-circle"
    />

    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12">
            <x-form.form
                action="{{ route('insumos.store') }}"
                method="POST"
                title="Informações do Insumo"
            >

                <div class="col-span-12 md:col-span-6">
                    <x-form.input
                        name="ds_nome"
                        label="Nome do Insumo"
                        required
                        :value="old('ds_nome')"
                        error="{{ $errors->first('ds_nome') }}"
                    />
                </div>
                <div class="col-span-12 md:col-span-6">
                    <x-form.select
                        name="tp_insumo"
                        label="Tipo de Insumo"
                        :options="\App\Enums\TipoInsumo::toSelectArray(true)"
                        required
                        error="{{ $errors->first('tp_insumo') }}"
                    />
                </div>
                <div class="col-span-12 md:col-span-6">
                    <x-form.input
                        name="ds_fabricante"
                        label="Fabricante"
                        :value="old('ds_fabricante')"
                        error="{{ $errors->first('ds_fabricante') }}"
                    />
                </div>
                <div class="col-span-12 md:col-span-6">
                    <x-form.select
                        name="tp_unidade_medida"
                        label="Unidade de Medida"
                        required
                        :options="\App\Enums\TipoUnidadeMedida::toSelectArray(true)"
                        error="{{ $errors->first('tp_unidade_medida') }}"
                    />
                </div>
                <div class="col-span-12 md:col-span-6">
                    <x-form.input
                        name="dt_validade"
                        label="Data de Validade"
                        type="date"
                        :value="old('dt_validade')"
                    />
                </div>
                <div class="col-span-12 md:col-span-6">
                    <x-form.input
                        name="nu_estoque_minimo"
                        label="Estoque Mínimo"
                        type="number"
                        min="0"
                        required
                        :value="old('nu_estoque_minimo')"
                        error="{{ $errors->first('nu_estoque_minimo') }}"
                    />
                </div>
                <div class="col-span-12">
                    <x-form.input
                        name="ds_composicao"
                        label="Composição"
                        type="textarea"
                        :value="old('ds_composicao')"
                        error="{{ $errors->first('ds_composicao') }}"
                    />
                </div>
            </x-form.form>
        </div>
    </div>


@endsection
