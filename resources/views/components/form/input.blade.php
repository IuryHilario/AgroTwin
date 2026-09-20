{{--
    Campo de formulário. Atributos que não são props (step, min, max,
    readonly, maxlength...) são repassados direto para o <input>.
    O input fica como filho direto do bloco: o modal.js insere a mensagem
    de erro da validação AJAX em input.parentNode.
--}}
@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'required' => false,
    'placeholder' => null,
    'error' => null,
    'ajuda' => null,
    'rows' => 4,
])

<div class="flex flex-col gap-1.5">
    @if ($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}@if ($required)<span class="ml-0.5 text-rose-500">*</span>@endif
        </label>
    @endif

    @if ($type === 'textarea')
        <textarea name="{{ $name }}" id="{{ $name }}" rows="{{ $rows }}" autocomplete="off"
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            {{ $attributes->class(['form-control', 'is-invalid' => $error]) }}>{{ old($name, $value) }}</textarea>
    @else
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $value) }}" autocomplete="off"
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            {{ $attributes->class(['form-control', 'is-invalid' => $error]) }}>
    @endif

    @if ($ajuda && !$error)
        <p class="text-xs text-muted">{{ $ajuda }}</p>
    @endif

    @if ($error)
        <div class="invalid-feedback">{{ $error }}</div>
    @endif
</div>
