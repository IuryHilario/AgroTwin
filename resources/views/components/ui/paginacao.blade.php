{{--
    Rodapé de paginação das listagens. Mostra o intervalo exibido e as páginas;
    some quando tudo cabe numa página só.
--}}
@props(['paginador'])

@if ($paginador->hasPages())
    <nav class="mt-4 flex flex-col items-center justify-between gap-3 sm:flex-row" aria-label="Paginação">
        <p class="text-sm text-muted">
            <span class="font-readout">{{ $paginador->firstItem() }}</span>–<span class="font-readout">{{ $paginador->lastItem() }}</span>
            de <span class="font-readout">{{ $paginador->total() }}</span>
        </p>

        <div class="flex items-center gap-1">
            @if ($paginador->onFirstPage())
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-300 dark:text-gray-600">
                    <i class="fas fa-chevron-left text-xs"></i>
                </span>
            @else
                <a href="{{ $paginador->previousPageUrl() }}" rel="prev" aria-label="Página anterior"
                   class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 transition-colors hover:bg-gray-100 hover:text-green-600 dark:text-gray-400 dark:hover:bg-gray-700">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
            @endif

            @foreach ($paginador->onEachSide(1)->linkCollection()->slice(1, -1) as $link)
                @if ($link['url'] === null)
                    <span class="px-2 text-sm text-muted">{{ $link['label'] }}</span>
                @elseif ($link['active'])
                    <span aria-current="page"
                          class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg bg-green-600 px-2 font-readout text-sm font-semibold text-white">
                        {{ $link['label'] }}
                    </span>
                @else
                    <a href="{{ $link['url'] }}"
                       class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg px-2 font-readout text-sm text-gray-600 transition-colors hover:bg-gray-100 hover:text-green-600 dark:text-gray-300 dark:hover:bg-gray-700">
                        {{ $link['label'] }}
                    </a>
                @endif
            @endforeach

            @if ($paginador->hasMorePages())
                <a href="{{ $paginador->nextPageUrl() }}" rel="next" aria-label="Próxima página"
                   class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 transition-colors hover:bg-gray-100 hover:text-green-600 dark:text-gray-400 dark:hover:bg-gray-700">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
            @else
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-300 dark:text-gray-600">
                    <i class="fas fa-chevron-right text-xs"></i>
                </span>
            @endif
        </div>
    </nav>
@endif
