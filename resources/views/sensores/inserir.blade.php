@extends('layouts.app')

@section('title', 'Novo Sensor - AgroTwin')

@section('content')

    <x-ui.section-header
        :buttons="[
            [
                'route' => route('sensores.index'),
                'text' => 'Voltar Sensores',
                'icon' => 'fas fa-arrow-left'
            ],
        ]"
        title="Novo Sensor"
        icon="fas fa-plus-circle"
    />

    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12">
            <x-form.form
                action="{{ route('sensores.store') }}"
                method="POST"
                title="Informações do Sensor"
            >

                <div class="col-span-12 md:col-span-6">
                    <x-form.input
                        name="ds_nome"
                        label="Nome do Sensor"
                        type="text"
                        required
                        :value="old('ds_nome')"
                        error="{{ $errors->first('ds_nome') }}"
                    />
                </div>

                <div class="col-span-12 md:col-span-6">
                    <x-form.select
                        name="tp_sensor"
                        label="Tipo de Sensor"
                        :options="\App\Enums\TipoSensor::toSelectArray(true)"
                        :value="old('tp_sensor')"
                        error="{{ $errors->first('tp_sensor') }}"
                        required
                    />
                </div>

                <div class="col-span-12 md:col-span-6">
                    <x-form.select
                        name="id_propriedade"
                        label="Propriedade"
                        :options="['' => 'Selecione uma Propriedade'] + $propriedades->toArray()"
                        :value="old('id_propriedade')"
                        error="{{ $errors->first('id_propriedade') }}"
                        required
                    />
                </div>

                <div class="col-span-12 md:col-span-6">
                    <label for="id_lavoura" class="form-label">
                        Lavoura
                        <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="id_lavoura"
                        id="id_lavoura"
                        class="form-control{{ $errors->has('id_lavoura') ? ' is-invalid' : '' }}"
                        required
                        disabled
                    >
                        <option value="">Selecione uma propriedade primeiro</option>
                        @foreach($lavouras as $lavoura)
                            <option
                                value="{{ $lavoura->id_lavoura }}"
                                data-propriedade="{{ $lavoura->id_propriedade }}"
                                {{ (string) old('id_lavoura') === (string) $lavoura->id_lavoura ? 'selected' : '' }}
                            >
                                {{ $lavoura->ds_cultura }}
                            </option>
                        @endforeach
                    </select>
                    @if($errors->has('id_lavoura'))
                        <div class="invalid-feedback">{{ $errors->first('id_lavoura') }}</div>
                    @endif
                </div>
            </x-form.form>
        </div>
    </div>

@endsection

@push('scripts')
    @vite(['resources/js/sensores/sensor-form.js'])
@endpush
