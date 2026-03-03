{{-- Componente utilizado para o metodo do input --}}

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}@if($required) <span class="text-danger">*</span> @endif
        </label>
    @endif

    @if($type === 'textarea')
        <textarea
            name="{{ $name }}"
            id="{{ $name }}"
            class="form-control{{ $error ? ' is-invalid' : '' }}"
            {{-- @if($required) required @endif --}}
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            autocomplete="off"
            rows="4"
        >{{ old($name, $value) }}</textarea>
    @else
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            class="form-control{{ $error ? ' is-invalid' : '' }}"
            value="{{ old($name, $value) }}"
            {{-- @if($required) required @endif --}}
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            autocomplete="off"
        >
    @endif

    @if($error)
        <div class="invalid-feedback">
            {{ $error }}
        </div>
    @endif
</div>
