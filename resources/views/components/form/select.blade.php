{{-- Select de formulário. Mesma estrutura do <x-form.input>. --}}
@props([
    'name',
    'label' => null,
    'options' => [],
    'value' => null,
    'required' => false,
    'error' => null,
    'ajuda' => null,
])

<div class="flex flex-col gap-1.5">
    @if ($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}@if ($required)<span class="ml-0.5 text-rose-500">*</span>@endif
        </label>
    @endif

    <select name="{{ $name }}" id="{{ $name }}" {{ $attributes->class(['form-control', 'is-invalid' => $error]) }}>
        @foreach ($options as $valorOpcao => $rotuloOpcao)
            <option value="{{ $valorOpcao }}" @selected((string) old($name, $value) === (string) $valorOpcao)>
                {{ $rotuloOpcao }}
            </option>
        @endforeach
    </select>

    @if ($ajuda && !$error)
        <p class="text-xs text-muted">{{ $ajuda }}</p>
    @endif

    @if ($error)
        <div class="invalid-feedback">{{ $error }}</div>
    @endif
</div>
