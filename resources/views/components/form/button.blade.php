{{-- Componente para o uso de botões --}}

<div class="button-container">
    <button type="{{ $type }}" class="{{ $class }}">
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif
        {{ $text }}
    </button>
</div>
