{{-- Componente utilizado para o metodo do select --}}

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        class="form-control{{ $error ? ' is-invalid' : '' }}"
        {{-- @if($required) required @endif --}}
    >
        @foreach($options as $optionValue => $optionLabel)
            <option
                value="{{ $optionValue }}"
                {{ old($name, $value) == $optionValue ? 'selected' : '' }}
            >
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    @if($error)
        <div class="invalid-feedback">{{ $error }}</div>
    @endif
</div>
