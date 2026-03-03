{{-- Componente reutilizável de cabeçalho de seção com múltiplos botões --}}

<section class="mdl-grid section-header">
    <div class="mdl-cell mdl-cell--6-col" style="display: flex; align-items: center;">
        <h1 class="mdl-typography--title" style="margin: 0;">
            @if ($icon)
                <i class="{{ $icon }}" style="margin-right: 8px;"></i>
            @endif
            {{ $title }}
        </h1>
    </div>
</section>
