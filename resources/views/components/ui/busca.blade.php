{{--
    Campo de busca da listagem. Envia por GET no próprio endereço da tela e
    preserva os outros parâmetros da URL (filtros, por exemplo).
--}}
@props(['valor' => '', 'placeholder' => 'Buscar...'])

@php
    $outrosParametros = collect(request()->query())->except(['busca', 'page']);
@endphp

<form method="GET" class="relative w-full sm:max-w-xs">
    @foreach ($outrosParametros as $nome => $conteudo)
        <input type="hidden" name="{{ $nome }}" value="{{ $conteudo }}">
    @endforeach

    <i class="fas fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted"></i>

    <input
        type="search"
        name="busca"
        value="{{ $valor }}"
        placeholder="{{ $placeholder }}"
        aria-label="{{ $placeholder }}"
        class="form-control !pl-9 {{ $valor !== '' ? '!pr-9' : '' }}"
    >

    @if ($valor !== '')
        <a href="{{ request()->url() . ($outrosParametros->isNotEmpty() ? '?' . http_build_query($outrosParametros->all()) : '') }}"
           title="Limpar busca"
           class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-muted hover:text-heading">
            <i class="fas fa-xmark"></i>
        </a>
    @endif
</form>
