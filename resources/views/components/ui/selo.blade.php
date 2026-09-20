{{-- Selo de status. tom: ok | alerta | erro | info | neutro --}}
@props(['tom' => 'neutro'])

@php
    $cores = match ($tom) {
        'ok' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300',
        'alerta' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300',
        'erro' => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-900/30 dark:text-rose-300',
        'info' => 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300',
        default => 'bg-gray-100 text-gray-600 ring-gray-500/20 dark:bg-gray-700/60 dark:text-gray-300',
    };

    $ponto = match ($tom) {
        'ok' => 'bg-emerald-500',
        'alerta' => 'bg-amber-500',
        'erro' => 'bg-rose-500',
        'info' => 'bg-sky-500',
        default => 'bg-gray-400',
    };
@endphp

<span {{ $attributes->class("inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset $cores") }}>
    <span class="h-1.5 w-1.5 rounded-full {{ $ponto }}"></span>
    {{ $slot }}
</span>
