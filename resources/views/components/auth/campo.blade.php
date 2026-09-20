{{--
    Campo das telas de autenticação: rótulo com ícone, input, botão de
    mostrar/ocultar (quando é senha) e a mensagem de erro do Laravel.
    O clique do olho é tratado pelo listener delegado em layouts/auth.blade.php.
--}}
@props([
    'name',
    'label',
    'icone' => 'fa-circle',
    'type' => 'text',
    'value' => null,
    'autofocus' => false,
    'autocomplete' => null,
])

@php $ehSenha = $type === 'password'; @endphp

<div class="flex flex-col gap-1.5">
    <label for="{{ $name }}" class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
        <i class="fas {{ $icone }} w-3.5 text-center text-xs text-green-500"></i>
        {{ $label }}
    </label>

    <div class="relative">
        <input
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ $ehSenha ? '' : old($name, $value) }}"
            @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            @if($autofocus) autofocus @endif
            required
            class="form-control @error($name) is-invalid @enderror {{ $ehSenha ? 'pr-11' : '' }}"
        >

        @if($ehSenha)
            <button type="button" data-alternar-senha="{{ $name }}" aria-label="Mostrar senha"
                class="absolute right-3 top-1/2 flex -translate-y-1/2 items-center text-gray-400 transition-colors hover:text-green-500">
                <i class="fas fa-eye"></i>
            </button>
        @endif
    </div>

    @error($name)
        <span class="text-sm font-medium text-red-500">{{ $message }}</span>
    @enderror
</div>
