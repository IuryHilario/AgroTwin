{{-- Item de menu reutilizável da sidebar --}}

@props(['route', 'active', 'icon', 'label'])

@php
    $isActive = request()->routeIs($active);
@endphp

<li class="mb-1.5">
    <a href="{{ $route }}" title="{{ $label }}"
        class="menu-link flex items-center gap-3.5 rounded-xl px-5 py-3.5 font-medium no-underline transition-all duration-300 hover:translate-x-1 {{ $isActive ? 'border-l-4 border-green-600 bg-green-50 text-green-600 shadow-[0_4px_12px_rgba(34,197,94,0.2)] dark:border-green-500 dark:bg-green-900/30 dark:text-green-400' : 'text-gray-500 hover:bg-green-50 hover:text-green-600 hover:shadow-[0_4px_12px_rgba(34,197,94,0.15)] dark:text-gray-400 dark:hover:bg-green-900/20 dark:hover:text-green-400' }}">
        <i class="{{ $icon }} w-5 text-center"></i>
        <span class="menu-label">{{ $label }}</span>
    </a>
</li>
