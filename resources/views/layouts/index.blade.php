@extends('layouts.app')

@section('content')
    @yield('page-content')

    <!-- FAB button -->
    @if(isset($fabRoute) && isset($fabText))
        <a href="{{ $fabRoute }}"
           class="fixed bottom-5 right-5 z-[1000] flex h-14 w-14 items-center justify-center rounded-full bg-green-600 text-lg text-white shadow-lg transition-all duration-300 hover:-translate-y-0.5 hover:bg-green-700 hover:shadow-xl"
           title="{{ $fabText }}">
            <i class="fas fa-plus"></i>
        </a>
    @endif
@endsection
