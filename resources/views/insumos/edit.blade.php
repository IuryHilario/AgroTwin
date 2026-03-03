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
    <div class="mdl-grid">
        <div class="mdl-cell mdl-cell--12-col">
            <x-form.form
                action="{{ route('insumos.update', $insumo->id_insumo) }}"
                method="PUT"
                class="mdl-grid"
                title="Informações do Insumo"
            >
                <div class="mdl-cell mdl-cell--6-col">
                    <x-form.input
                        name="ds_nome"
                        label="Nome do Insumo"
                        required
                        :value="old('ds_nome', $insumo->ds_nome)"
                    />
                </div>
                <div class="mdl-cell mdl-cell--6-col">
                    <x-form.select
                        name="tp_insumo"
                        label="Tipo de Insumo"
                        :options="\App\Enums\TipoInsumo::toSelectArray(true)"
                        required
                        :value="old('tp_insumo', $insumo->tp_insumo?->value)"
                    />
                </div>
                <div class="mdl-cell mdl-cell--6-col">
                    <x-form.input
                        name="ds_fabricante"
                        label="Fabricante"
                        :value="old('ds_fabricante', $insumo->ds_fabricante)"
                    />
                </div>
                <div class="mdl-cell mdl-cell--6-col">
                    <x-form.select
                        name="tp_unidade_medida"
                        label="Unidade de Medida"
                        :options="\App\Enums\TipoUnidadeMedida::toSelectArray(true)"
                        :value="old('tp_unidade_medida', $insumo->tp_unidade_medida?->value)"
                        required
                    />
                </div>
                <div class="mdl-cell mdl-cell--6-col">
                    <x-form.input
                        name="dt_validade"
                        label="Data de Validade"
                        type="date"
                        :value="old('dt_validade', $insumo->dt_validade)"
                    />
                </div>
                <div class="mdl-cell mdl-cell--12-col">
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
