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

<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog {{ $size }}">
        <div class="modal-content" style="max-height: 90vh; display: flex; flex-direction: column;">
            <div class="modal-header {{ $bgColor }} text-white" style="flex-shrink: 0;">
                <h5 class="modal-title" id="{{ $modalId }}Label">
                    <i class="{{ $icon }} me-2"></i>
                    {{ $title }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" style="flex: 1; overflow-y: auto; max-height: calc(90vh - 120px);">
                @if($item)
                    <!-- Slot para conteúdo específico da funcionalidade -->
                    {{ $slot }}
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p>{{ ucfirst($resourceName) }} não encontrado.</p>
                    </div>
                @endif
            </div>

            <div class="modal-footer" style="flex-shrink: 0;">
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
