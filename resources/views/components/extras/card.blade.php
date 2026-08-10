@props([
    'title' => null,
    'icon' => null,
    'color' => '#2196F3',
    'valor' => 0,
])

<div class="col-span-12 sm:col-span-6 lg:col-span-3">
    <div class="w-full rounded-2xl border-2 border-gray-900 bg-white shadow-[0_2px_10px_rgba(0,0,0,0.1)] dark:border-gray-100 dark:bg-gray-800">
        <div class="w-full p-6 text-center">
            <i class="fas {{ $icon }} fa-2x" style="color: {{ $color }};"></i>
            <h6 class="mb-1 mt-2 text-base">{{ $title }}</h6>
            <h4 class="m-0 text-xl font-semibold" style="color: {{ $color }};">{{ $valor }}</h4>
            <small class="text-muted">Este mês</small>
        </div>
    </div>
</div>