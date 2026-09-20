@extends('layouts.auth')

@section('title', 'Esqueci minha senha - AgroTwin')
@section('cabecalho', 'Esqueci minha senha')
@section('subtitulo', 'Informe seu e-mail e enviaremos um link para redefinir a senha.')

@section('formulario')
    <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-5">
        @csrf

        <div class="entrada" style="animation-delay: 80ms">
            <x-auth.campo name="email" label="E-mail" icone="fa-envelope" type="email" autocomplete="email" :autofocus="true" />
        </div>

        <button type="submit" class="btn btn-primary entrada mt-1 w-full" style="animation-delay: 120ms">
            <i class="fas fa-paper-plane"></i>
            Enviar link de redefinição
        </button>
    </form>
@endsection

@section('rodape')
    <a href="{{ route('login') }}" class="font-medium text-green-600 hover:underline dark:text-green-400">
        <i class="fas fa-arrow-left-long mr-1 text-xs"></i>Voltar para o login
    </a>
@endsection
