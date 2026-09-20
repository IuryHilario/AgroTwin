@extends('layouts.auth')

@section('title', 'Entrar - AgroTwin')
@section('cabecalho', 'Entrar na sua conta')
@section('subtitulo', 'Acompanhe o solo das suas lavouras em tempo real.')

@section('formulario')
    <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-5" autocomplete="off">
        @csrf

        <div class="entrada" style="animation-delay: 80ms">
            <x-auth.campo name="email" label="E-mail" icone="fa-envelope" type="email" autocomplete="username" :autofocus="true" />
        </div>

        <div class="entrada" style="animation-delay: 120ms">
            <x-auth.campo name="password" label="Senha" icone="fa-lock" type="password" autocomplete="current-password" />
        </div>

        <div class="entrada flex items-center justify-between gap-4" style="animation-delay: 160ms">
            <label class="flex cursor-pointer items-center gap-2 text-sm text-muted">
                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500 dark:border-gray-600">
                Lembrar-me
            </label>
            <a href="{{ route('password.request') }}" class="text-sm font-medium text-green-600 hover:underline dark:text-green-400">
                Esqueceu a senha?
            </a>
        </div>

        <button type="submit" class="btn btn-primary entrada mt-1 w-full" style="animation-delay: 200ms">
            <i class="fas fa-arrow-right-to-bracket"></i>
            Entrar
        </button>
    </form>
@endsection

@section('rodape')
    Não tem uma conta?
    <a href="{{ route('register') }}" class="font-medium text-green-600 hover:underline dark:text-green-400">Cadastre-se</a>
@endsection
