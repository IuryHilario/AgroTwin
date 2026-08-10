{{-- Componente para o uso de botões --}}

<button type="{{ $type }}" class="{{ $class }}">
    @if($icon)
        <i class="{{ $icon }}"></i>
    @endif
    {{ $text }}
</button>
