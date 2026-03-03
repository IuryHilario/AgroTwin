<table {{ $attributes->merge(['class' => 'mdl-data-table mdl-js-data-table mdl-shadow--2dp']) }} style="width:100%">
    <tbody>
        {{ $slot }}
    </tbody>
</table>
