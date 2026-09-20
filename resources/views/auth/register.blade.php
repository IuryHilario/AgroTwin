@extends('layouts.auth')

@section('title', 'Cadastro - AgroTwin')
@section('cabecalho', 'Criar conta')
@section('subtitulo', 'Cadastre-se para começar a monitorar o solo da sua propriedade.')

@section('formulario')
    <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-5">
        @csrf

        <div class="entrada" style="animation-delay: 80ms">
            <x-auth.campo name="name" label="Nome" icone="fa-user" autocomplete="name" :autofocus="true" />
        </div>

        <div class="entrada" style="animation-delay: 120ms">
            <x-auth.campo name="email" label="E-mail" icone="fa-envelope" type="email" autocomplete="email" />
        </div>

        <div class="entrada" style="animation-delay: 160ms">
            <x-auth.campo name="password" label="Senha" icone="fa-lock" type="password" autocomplete="new-password" />
        </div>

        <div class="entrada" style="animation-delay: 200ms">
            <x-auth.campo name="password_confirmation" label="Confirmar senha" icone="fa-lock" type="password" autocomplete="new-password" />
        </div>

        <button type="submit" class="btn btn-primary entrada mt-1 w-full" style="animation-delay: 240ms">
            <i class="fas fa-user-plus"></i>
            Criar conta
        </button>
    </form>
@endsection

@section('rodape')
    Já tem uma conta?
    <a href="{{ route('login') }}" class="font-medium text-green-600 hover:underline dark:text-green-400">Faça login</a>
@endsection
