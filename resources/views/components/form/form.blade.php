{{--
    Formulário de tela cheia (inserir/editar). Enviado via AJAX pelo modal.js
    (classe ajax-form), que mostra o resultado e segue o redirect do controller.
    Os campos entram no slot, dentro de um grid de 12 colunas.
--}}
@props([
    'action',
    'method' => 'POST',
    'title' => null,
    'descricao' => null,
])

<form action="{{ $action }}" method="POST" {{ $attributes->class('ajax-form form-section') }}>
    @csrf
    @if (in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
        @method($method)
    @endif

    @if ($title)
        <header class="border-b border-subtle pb-4">
            <h2 class="section-title">{{ $title }}</h2>
            @if ($descricao)
                <p class="mt-1 text-sm text-muted">{{ $descricao }}</p>
            @endif
        </header>
    @endif

    <div class="grid grid-cols-12 gap-x-4 gap-y-5">
        {{ $slot }}
    </div>

    <div class="form-actions">
        <button type="reset" class="btn btn-secondary">
            <i class="fas fa-eraser"></i> Limpar
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-check"></i> Salvar
        </button>
    </div>
</form>
