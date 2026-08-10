@extends('layouts.app')

@section('title', 'Meu Perfil - AgroTwin')

@section('content')

    <x-ui.section-header
        title="Meu Perfil"
        icon="fas fa-user-circle"
    />

    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 lg:col-span-6">
            <x-form.form
                action="{{ route('perfil.update') }}"
                method="PUT"
                title="Informações Pessoais"
            >
                <div class="col-span-12">
                    <x-form.input
                        name="name"
                        label="Nome"
                        type="text"
                        required
                        :value="old('name', $usuario->name)"
                        error="{{ $errors->first('name') }}"
                    />
                </div>

                <div class="col-span-12">
                    <x-form.input
                        name="email"
                        label="E-mail"
                        type="email"
                        required
                        :value="old('email', $usuario->email)"
                        error="{{ $errors->first('email') }}"
                    />
                </div>

                <div class="col-span-12">
                    <span class="form-label">Conta criada em</span>
                    <p class="text-sm text-muted">{{ $usuario->created_at->format('d/m/Y') }}</p>
                </div>
            </x-form.form>
        </div>

        <div class="col-span-12 lg:col-span-6">
            <x-form.form
                action="{{ route('perfil.senha') }}"
                method="PUT"
                title="Alterar Senha"
            >
                <div class="col-span-12">
                    <x-form.input
                        name="senha_atual"
                        label="Senha Atual"
                        type="password"
                        required
                        error="{{ $errors->first('senha_atual') }}"
                    />
                </div>

                <div class="col-span-12">
                    <x-form.input
                        name="nova_senha"
                        label="Nova Senha"
                        type="password"
                        required
                        error="{{ $errors->first('nova_senha') }}"
                    />
                </div>

                <div class="col-span-12">
                    <x-form.input
                        name="nova_senha_confirmation"
                        label="Confirmar Nova Senha"
                        type="password"
                        required
                    />
                </div>
            </x-form.form>
        </div>
    </div>

@endsection
