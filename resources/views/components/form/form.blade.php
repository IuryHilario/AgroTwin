{{-- Componente para a inserção de dados do método form --}}

<form class="form-section {{ $class ?? '' }}"
    action="{{ $action }}"
    method="POST"
>
    @csrf

    @if(isset($method) && in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
        @method($method)
    @endif

    <div>
        @if(!empty($title))
            <h3 class="section-tit le">{{ $title }}</h3>
        @endif

        <div class="mdl-grid">
            {{ $slot }}
            <div class="form-actions mdl-cell mdl-cell--12-col">
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

