{{-- Componente reutilizável de cabeçalho de seção com múltiplos botões --}}

@props(['buttons' => []])

<section class="mx-8 mb-2 mt-8 flex flex-wrap items-center justify-between gap-4 rounded-2xl bg-white p-5 text-gray-900 shadow-[0_2px_6px_rgba(0,0,0,0.2)] dark:bg-gray-800 dark:text-gray-100">
    <h1 class="m-0 flex items-center gap-3 text-[1.8rem] font-semibold">
        @if ($icon)
            <i class="{{ $icon }} text-green-600"></i>
        @endif
        {{ $title }}
    </h1>

    @if(count($buttons))
        <div class="flex flex-wrap gap-2">
            @foreach($buttons as $button)
                <a href="{{ $button['route'] }}" class="btn btn-secondary">
                    @if(isset($button['icon']))
                        <i class="{{ $button['icon'] }}"></i>
                    @endif
                    {{ $button['text'] }}
                </a>
            @endforeach
        </div>
    @endif
</section>
