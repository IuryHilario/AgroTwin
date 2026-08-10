{{-- Grade padronizada de campos rótulo/valor, usada nas telas de "detalhar" --}}

@props(['fields' => []])

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    @foreach($fields as $field)
        <div class="{{ ($field['full'] ?? false) ? 'sm:col-span-2' : '' }}">
            <span class="block text-xs font-medium uppercase tracking-wide text-muted">{{ $field['label'] }}</span>
            <span class="mt-0.5 block whitespace-pre-line text-sm font-medium text-gray-900 dark:text-gray-100">{{ $field['value'] ?? '-' }}</span>
        </div>
    @endforeach
</div>
