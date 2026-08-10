{{-- Componente para a inserção de dados do método form --}}

<form class="ajax-form form-section {{ $class ?? '' }}"
    action="{{ $action }}"
    method="POST"
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
            <div class="form-actions col-span-12">
                <x-form.button
                    type="reset"
                    class="btn btn-secondary"
                    text="Limpar"
                    icon="fas fa-eraser"
                />
                <x-form.button
                    type="submit"
                    class="btn btn-primary"
                    text="Salvar"
                    icon="fas fa-save"
                />
            </div>
        </div>
    </div>
</form>

