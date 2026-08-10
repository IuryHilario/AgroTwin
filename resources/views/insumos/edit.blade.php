@extends('layouts.app')

@section('title', 'Editar Insumo - AgroTwin')

@section('content')
    <x-ui.section-header
        :buttons="[
            [
                'route' => route('insumos.index'),
                'text' => 'Voltar Insumos',
                'icon' => 'fas fa-arrow-left'
            ],
        ]"
        title="Editar Insumo"
        icon="fas fa-edit"
    />
    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12">
            <x-form.form
                action="{{ route('insumos.update', $insumo->id_insumo) }}"
                method="PUT"
                title="Informações do Insumo"
            >
                <div class="col-span-12 md:col-span-6">
                    <x-form.input
                        name="ds_nome"
                        label="Nome do Insumo"
                        required
                        :value="old('ds_nome', $insumo->ds_nome)"
                    />
                </div>
                <div class="col-span-12 md:col-span-6">
                    <x-form.select
                        name="tp_insumo"
                        label="Tipo de Insumo"
                        :options="\App\Enums\TipoInsumo::toSelectArray(true)"
                        required
                        :value="old('tp_insumo', $insumo->tp_insumo?->value)"
                    />
                </div>
                <div class="col-span-12 md:col-span-6">
                    <x-form.input
                        name="ds_fabricante"
                        label="Fabricante"
                        :value="old('ds_fabricante', $insumo->ds_fabricante)"
                    />
                </div>
                <div class="col-span-12 md:col-span-6">
                    <x-form.select
                        name="tp_unidade_medida"
                        label="Unidade de Medida"
                        :options="\App\Enums\TipoUnidadeMedida::toSelectArray(true)"
                        :value="old('tp_unidade_medida', $insumo->tp_unidade_medida?->value)"
                        required
                    />
                </div>
                <div class="col-span-12 md:col-span-6">
                    <x-form.input
                        name="dt_validade"
                        label="Data de Validade"
                        type="date"
                        :value="old('dt_validade', $insumo->dt_validade?->format('Y-m-d'))"
                    />
                </div>
                <div class="col-span-12">
                    <x-form.input
                        name="ds_composicao"
                        label="Composição"
                        type="textarea"
                        :value="old('ds_composicao', $insumo->ds_composicao)"
                    />
                </div>
            </x-form.form>
        </div>
    </div>


@endsection
