@extends('layouts.auth')

@section('title', 'Redefinir senha - AgroTwin')
@section('cabecalho', 'Redefinir senha')
@section('subtitulo', 'Escolha uma nova senha de pelo menos 8 caracteres.')

@section('formulario')
    <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="entrada" style="animation-delay: 80ms">
            <x-auth.campo name="email" label="E-mail" icone="fa-envelope" type="email" :value="$email" autocomplete="email" />
        </div>

        <div class="entrada" style="animation-delay: 120ms">
            <x-auth.campo name="password" label="Nova senha" icone="fa-lock" type="password" autocomplete="new-password" :autofocus="true" />
        </div>

        <div class="entrada" style="animation-delay: 160ms">
            <x-auth.campo name="password_confirmation" label="Confirmar nova senha" icone="fa-lock" type="password" autocomplete="new-password" />
        </div>

        <button type="submit" class="btn btn-primary entrada mt-1 w-full" style="animation-delay: 200ms">
            <i class="fas fa-check"></i>
            Redefinir senha
        </button>
    </form>
@endsection

@section('rodape')
    <a href="{{ route('login') }}" class="font-medium text-green-600 hover:underline dark:text-green-400">
        <i class="fas fa-arrow-left-long mr-1 text-xs"></i>Voltar para o login
    </a>
@endsection
