{{-- Item de link reutilizável do menu suspenso do usuário --}}

@props(['route', 'icon', 'label', 'divider' => false, 'onclick' => null])

@if ($divider)
    <div class="my-2 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent dark:via-gray-700"></div>
@endif

<a href="{{ $route }}"
    @if($onclick) onclick="{{ $onclick }}" @endif
    class="flex items-center gap-3 px-[18px] py-3.5 font-medium text-gray-700 no-underline transition-all duration-200 hover:bg-gradient-to-br hover:from-gray-50 hover:to-gray-100 hover:text-green-500 dark:text-gray-200 dark:hover:from-gray-700 dark:hover:to-gray-700">
    <i class="{{ $icon }}"></i>
    {{ $label }}
</a>
