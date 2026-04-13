@props([
    'title' => null,
    'icon' => null,
    'color' => '#2196F3',
    'valor' => 0,
])

<div class="mdl-cell mdl-cell--3-col">
    <div class="mdl-card mdl-shadow--2dp" style="width: 100%; border: 2px solid #000; border-radius: 16px;">
        <div class="mdl-card__supporting-text" style="text-align: center; width: 100%;">
            <i class="fas {{ $icon }} fa-2x" style="color: {{ $color }};"></i>
            <h6 style="margin: 8px 0 4px;">{{ $title }}</h6>
            <h4 style="margin: 0; color: {{ $color }};">{{ $valor }}</h4>
            <small class="mdl-color-text--grey-600">Este mês</small>
        </div>
    </div>
</div>