@extends('layouts.app')

@section('content')
    {{-- Mesmo container do dashboard, para as telas de listagem alinharem com ele --}}
    <div class="mx-auto max-w-[1400px]">
        @yield('page-content')
    </div>

    {{-- Botão flutuante de criar: só no mobile; no desktop a ação fica no cabeçalho --}}
    @if(isset($fabRoute) && isset($fabText))
        <a href="{{ $fabRoute }}"
           class="fixed bottom-5 right-5 z-[1000] flex h-14 w-14 sm:hidden items-center justify-center rounded-full bg-green-600 text-lg text-white shadow-lg transition-all duration-300 hover:-translate-y-0.5 hover:bg-green-700 hover:shadow-xl"
           title="{{ $fabText }}">
            <i class="fas fa-plus"></i>
        </a>
    @endif
@endsection
