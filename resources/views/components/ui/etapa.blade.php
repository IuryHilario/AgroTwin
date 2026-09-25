{{--
    Uma etapa do <x-ui.stepper>. Os campos entram no slot, num grid de 12 colunas
    (mesmo do <x-form.form>).

    Regras que o HTML não expressa vão num listener do evento "etapa-validar"
    disparado neste painel — chame event.preventDefault() e, se quiser, preencha
    event.detail.erro com a mensagem.
--}}
@props([
    'numero',
    'titulo' => null,
    'descricao' => null,
])

<section
    data-etapa="{{ $numero }}"
    x-show="atual === {{ $numero }}"
    x-cloak
    @if ($titulo) aria-labelledby="etapa-{{ $numero }}-titulo" @endif
    {{ $attributes->class('flex flex-col gap-6') }}
>
    @if ($titulo)
        <header class="border-b border-subtle pb-4">
            <h2 id="etapa-{{ $numero }}-titulo" data-etapa-titulo tabindex="-1" class="section-title focus:outline-none">
                {{ $titulo }}
            </h2>
            @if ($descricao)
                <p class="mt-1 text-sm text-muted">{{ $descricao }}</p>
            @endif
        </header>
    @endif

    <div class="grid grid-cols-12 gap-x-4 gap-y-5">
        {{ $slot }}
    </div>
</section>
