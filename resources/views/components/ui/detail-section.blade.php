{{-- Seção padronizada (título + ícone + conteúdo livre), usada nas telas de "detalhar" --}}

@props(['title', 'icon' => null])

<div class="mb-6 last:mb-0">
    <h6 class="mb-3 flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-muted">
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif
        {{ $title }}
    </h6>
    {{ $slot }}
</div>
