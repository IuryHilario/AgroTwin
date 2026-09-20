{{--
    Formulário dentro de modal: mesmo envio AJAX do <x-form.form>, mas sem
    botões próprios — o botão de salvar fica no rodapé do modal e aponta
    para este form pelo atributo form="{id}".
--}}
@props([
    'action',
    'method' => 'POST',
    'title' => null,
])

<form action="{{ $action }}" method="POST" {{ $attributes->class('ajax-form flex flex-col gap-5') }}>
    @csrf
    @if (in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
        @method($method)
    @endif

    @if ($title)
        <h3 class="section-title">{{ $title }}</h3>
    @endif

    <div class="grid grid-cols-12 gap-x-4 gap-y-5">
        {{ $slot }}
    </div>
</form>
