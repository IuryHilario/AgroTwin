@props([
    'modalId',
    'title',
    'icon' => 'fas fa-eye',
    'bgColor' => 'bg-success',
    'size' => 'modal-lg', // Opções: modal-sm, modal-lg, modal-xl
    'item' => null,
    'resourceName' => '',
    'editRoute' => null,
    'additionalButtons' => []
])

@php
    $sizeClasses = match($size) {
        'modal-sm' => 'max-w-md',
        'modal-xl' => 'max-w-5xl',
        default => 'max-w-2xl', // modal-lg
    };

    $headerClasses = match($bgColor) {
        'bg-dark' => 'bg-gray-800 text-white',
        default => 'bg-green-600 text-white', // bg-success
    };
@endphp

<div
    id="{{ $modalId }}"
    class="modal fixed inset-0 z-[1050] flex items-center justify-center p-4"
    x-data="{ open: true }"
    x-show="open"
    x-cloak
    x-on:keydown.escape.window="open = false; $dispatch('modal-closed')"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
>
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/50" x-on:click="open = false; $dispatch('modal-closed')"></div>

    <!-- Dialog -->
    <div
        class="modal-dialog relative w-full {{ $sizeClasses }}"
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
    >
        <div class="modal-content flex max-h-[90vh] w-full flex-col overflow-hidden rounded-xl bg-white shadow-2xl dark:bg-gray-800">
            <div class="flex flex-shrink-0 items-center justify-between px-6 py-4 {{ $headerClasses }}">
                <h5 class="flex items-center text-lg font-semibold" id="{{ $modalId }}Label">
                    <i class="{{ $icon }} me-2"></i>
                    {{ $title }}
                </h5>
                <button
                    type="button"
                    class="btn-close text-white/80 hover:text-white"
                    aria-label="Close"
                    x-on:click="open = false; $dispatch('modal-closed')"
                >
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <div class="modal-body flex-1 overflow-y-auto p-6">
                @if($item)
                    <!-- Slot para conteúdo específico da funcionalidade -->
                    {{ $slot }}
                @else
                    <div class="py-8 text-center text-muted">
                        <i class="fas fa-exclamation-triangle mb-2 block text-3xl"></i>
                        <p>{{ ucfirst($resourceName) }} não encontrado.</p>
                    </div>
                @endif
            </div>

            <div class="modal-footer flex flex-shrink-0 justify-end gap-2 border-t border-gray-100 px-6 py-4 dark:border-gray-700">
                @if($item)
                    @if($editRoute)
                        <a href="{{ $editRoute }}" class="btn btn-primary edit-link">
                            <i class="fas fa-edit me-1"></i>
                            Editar
                        </a>
                    @endif

                    @foreach($additionalButtons as $button)
                        <{{ $button['tag'] ?? 'button' }}
                            type="{{ $button['type'] ?? 'button' }}"
                            class="btn {{ $button['class'] ?? 'btn-secondary' }}"
                            @if(isset($button['href'])) href="{{ $button['href'] }}" @endif
                            @if(isset($button['onclick'])) onclick="{{ $button['onclick'] }}" @endif
                            @if(isset($button['data-action'])) data-action="{{ $button['data-action'] }}" @endif
                            @if(isset($button['data-url'])) data-url="{{ $button['data-url'] }}" @endif
                            @if(isset($button['data-movimentacao'])) data-movimentacao="{{ $button['data-movimentacao'] }}" @endif
                            @if(isset($button['form'])) form="{{ $button['form'] }}" @endif>
                            @if(isset($button['icon']))
                                <i class="{{ $button['icon'] }} me-1"></i>
                            @endif
                            {{ $button['text'] }}
                        </{{ $button['tag'] ?? 'button' }}>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
