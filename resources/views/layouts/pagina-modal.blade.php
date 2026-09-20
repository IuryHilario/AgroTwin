{{--
    Abriga, como página inteira, as views que normalmente aparecem em modal
    (detalhar, limites, estoque, monitor...). Abrir a URL direto — por um link
    colado, um favorito ou um F5 — antes entregava o HTML do modal sem layout
    nem CSS. Aqui ele vem dentro do layout, e fechar volta para a tela anterior.
--}}
@extends('layouts.app')

@section('title', $titulo ?? 'AgroTwin')

@section('content')
    <div x-data @modal-closed="window.location.href = '{{ $voltarPara }}'">
        {!! $conteudo !!}
    </div>
@endsection
