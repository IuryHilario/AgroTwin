@extends('layouts.app')

@section('title', 'Editar Lavoura - AgroTwin')

@section('content')

    <x-ui.section-header
        :buttons="[
            [
                'route' => route('lavouras.index'),
                'text' => 'Voltar Lavouras',
                'icon' => 'fas fa-arrow-left'
            ],
        ]"
        title="Editar Lavoura"
        icon="fas fa-plus-circle"
    />

    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12">
            <x-form.form
                action="{{ route('lavouras.update', $lavoura->id_lavoura) }}"
                method="PUT"
                title="Informações da Lavoura"
            >

                <div class="col-span-12">
                    <x-form.input
                        name="ds_cultura"
                        label="Descrição da Lavoura"
                        type="text"
                        required
                        :value="old('ds_cultura', $lavoura->ds_cultura ?? '')"
                        error="{{ $errors->first('ds_cultura') }}"
                    />
                </div>
                <div class="col-span-12">
                    <x-form.select
                        name="id_propriedade"
                        label="Propriedade"
                        :options="['' => 'Selecione uma Propriedade'] + $propriedades->toArray()"
                        :value="old('id_propriedade', $lavoura->id_propriedade ?? '')"
                        error="{{ $errors->first('id_propriedade') }}"
                    />
                </div>

                <div class="col-span-12 md:col-span-6">
                    <x-form.input
                        name="nu_intervalo_leitura_minutos"
                        label="Intervalo de leitura dos sensores (minutos)"
                        type="number"
                        :value="old('nu_intervalo_leitura_minutos', $lavoura->nu_intervalo_leitura_minutos ?? 5)"
                        error="{{ $errors->first('nu_intervalo_leitura_minutos') }}"
                    />
                </div>

                <div class="col-span-12">
                    <x-form.input
                        name="ds_observacao"
                        label="Observações"
                        type="textarea"
                        :value="old('ds_observacao', $lavoura->ds_observacao ?? '')"
                        error="{{ $errors->first('ds_observacao') }}"
                    />
                </div>
            </x-form.form>
        </div>
    </div>

@endsection
