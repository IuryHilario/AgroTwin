{{-- Componente para formulários dentro de modais (sem botões internos) --}}

<form class="ajax-form form-section {{ $class ?? '' }}"
    action="{{ $action }}"
    method="POST"
    id="{{ $id ?? '' }}"
>
    @csrf

    @if(isset($method) && in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
        @method($method)
    @endif

    <div>
        @if(!empty($title))
            <h3 class="section-title">{{ $title }}</h3>
        @endif

        <div class="grid grid-cols-12 gap-4">
            {{ $slot }}
        </div>
    </div>
</form>
