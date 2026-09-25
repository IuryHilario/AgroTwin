{{--
    Formulário em etapas. Vai dentro de um <form> (normalmente <x-form.form :acoes="false">)
    e recebe as etapas como filhos <x-ui.etapa :numero="n">.

    Props:
      etapas          — lista com o rótulo de cada etapa. Aceita strings ou
                        ['titulo' => ..., 'icone' => 'fa-...'].
      navegacaoLivre  — true libera todas as etapas no cabeçalho desde o início
                        (edição, quando tudo já está preenchido).
      rotuloEnviar    — texto do botão da última etapa.

    Cada etapa é validada antes de avançar: a validação nativa do HTML
    (required, min, step, pattern...) e o evento cancelável "etapa-validar"
    para regras próprias. Detalhes em resources/js/componentes/stepper.js.
--}}
@props([
    'etapas' => [],
    'navegacaoLivre' => false,
    'rotuloEnviar' => 'Salvar',
])

@php
    $etapas = collect($etapas)->values()->map(fn ($etapa) => is_array($etapa)
        ? $etapa + ['icone' => null]
        : ['titulo' => $etapa, 'icone' => null]);
    $total = $etapas->count();
@endphp

<div
    x-data="stepper({ total: {{ $total }}, navegacaoLivre: {{ $navegacaoLivre ? 'true' : 'false' }} })"
    {{ $attributes->class('flex flex-col gap-6 scroll-mt-24') }}
>
    {{-- Progresso --}}
    <nav aria-label="Etapas do formulário">
        <ol class="flex items-start">
            @foreach ($etapas as $indice => $etapa)
                @php $numero = $indice + 1; @endphp
                <li class="flex flex-1 items-start last:flex-none">
                    <button
                        type="button"
                        @click="irPara({{ $numero }})"
                        :disabled="!podeIrPara({{ $numero }})"
                        :aria-current="atual === {{ $numero }} ? 'step' : null"
                        class="group flex flex-col items-center gap-2 disabled:cursor-not-allowed sm:flex-row sm:gap-3"
                    >
                        <span
                            class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full border-2 text-sm font-semibold transition-colors"
                            :class="{
                                'border-emerald-600 bg-emerald-600 text-white shadow-sm shadow-emerald-600/30': estado({{ $numero }}) === 'atual',
                                'border-emerald-600 bg-emerald-50 text-emerald-700 group-hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300': estado({{ $numero }}) === 'concluida' || estado({{ $numero }}) === 'liberada',
                                'border-gray-300 bg-white text-gray-400 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-500': estado({{ $numero }}) === 'pendente',
                            }"
                        >
                            <i x-show="estado({{ $numero }}) === 'concluida'" class="fas fa-check text-xs"></i>
                            <span x-show="estado({{ $numero }}) !== 'concluida'" class="font-readout">
                                @if ($etapa['icone'])
                                    <i class="fas {{ $etapa['icone'] }} text-xs"></i>
                                @else
                                    {{ $numero }}
                                @endif
                            </span>
                        </span>
                        <span
                            class="text-center text-xs font-medium sm:text-left sm:text-sm"
                            :class="estado({{ $numero }}) === 'pendente' ? 'text-muted' : 'text-heading'"
                        >
                            <span class="hidden sm:inline">{{ $etapa['titulo'] }}</span>
                            {{-- No mobile só a etapa atual mostra o nome, senão o rótulo espreme os círculos. --}}
                            <span class="sm:hidden" x-show="atual === {{ $numero }}">{{ $etapa['titulo'] }}</span>
                        </span>
                    </button>

                    @if (! $loop->last)
                        <span
                            aria-hidden="true"
                            class="mx-2 mt-[17px] h-0.5 flex-1 rounded-full transition-colors sm:mx-4"
                            :class="atual > {{ $numero }} ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-gray-700'"
                        ></span>
                    @endif
                </li>
            @endforeach
        </ol>

        <p class="sr-only" aria-live="polite" x-text="`Etapa ${atual} de {{ $total }}`"></p>
    </nav>

    {{-- Etapas --}}
    <div>{{ $slot }}</div>

    {{-- Erro das regras próprias da etapa (evento etapa-validar) --}}
    <div
        x-show="erro"
        x-cloak
        x-transition.opacity
        role="alert"
        class="flex items-start gap-2.5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/60 dark:bg-rose-900/20 dark:text-rose-300"
    >
        <i class="fas fa-circle-exclamation mt-0.5"></i>
        <span x-text="erro"></span>
    </div>

    {{-- Navegação --}}
    <div class="form-actions !justify-between">
        <button type="button" class="btn btn-secondary" @click="voltar()" x-show="atual > 1" x-cloak>
            <i class="fas fa-arrow-left"></i> Voltar
        </button>
        <span x-show="atual === 1" class="hidden sm:block"></span>

        <div class="flex flex-col-reverse items-stretch gap-3 sm:flex-row sm:items-center">
            <span class="font-readout text-center text-xs text-muted sm:text-right">
                Etapa <span x-text="atual"></span> de {{ $total }}
            </span>
            <button type="button" class="btn btn-primary" @click="avancar()" x-show="!ultima">
                Próximo <i class="fas fa-arrow-right"></i>
            </button>
            <button type="submit" class="btn btn-primary" x-show="ultima" x-cloak>
                <i class="fas fa-check"></i> {{ $rotuloEnviar }}
            </button>
        </div>
    </div>
</div>
