@extends('layouts.app')

@section('content')
    @yield('page-content')

    <!-- FAB button -->
    @if(isset($fabRoute) && isset($fabText))
        <a href="{{ $fabRoute }}" class="mdl-button mdl-js-button mdl-button--fab mdl-button--colored"
           title="{{ $fabText }}" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
            <i class="material-icons">add</i>
        </a>
    @endif
@endsection
