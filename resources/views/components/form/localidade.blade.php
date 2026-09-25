{{--
    Seletor de localidade: busca de município (Open-Meteo), GPS do navegador e
    mapa com pino arrastável. Grava latitude, longitude e um rótulo legível.

    Props:
      latitude, longitude, rotulo — valores atuais (edição).
      obrigatorio                 — dentro de um <x-ui.stepper>, impede avançar sem ponto e
                                    sem nome do local (o seletor valida os dois, nessa ordem;
                                    o nome não leva `required` nativo para não passar na frente).
      campoLatitude, campoLongitude, campoRotulo — nomes dos campos enviados.
      errorLatitude, errorRotulo  — mensagens de erro do servidor.

    Reutilizável em qualquer formulário; o comportamento está em
    resources/js/componentes/localidade.js.
--}}
@props([
    'latitude' => null,
    'longitude' => null,
    'rotulo' => '',
    'obrigatorio' => false,
    'campoLatitude' => 'nu_latitude',
    'campoLongitude' => 'nu_longitude',
    'campoRotulo' => 'ds_localizacao',
    'errorLatitude' => null,
    'errorRotulo' => null,
])

@php
    $configuracao = [
        'latitude' => old($campoLatitude, $latitude),
        'longitude' => old($campoLongitude, $longitude),
        'rotulo' => old($campoRotulo, $rotulo) ?? '',
        'obrigatorio' => (bool) $obrigatorio,
        'urlBusca' => route('localidades.buscar'),
    ];
@endphp

<div x-data="seletorLocalidade({{ Js::from($configuracao) }})" {{ $attributes->class('flex flex-col gap-4') }}>

    {{-- Busca de município --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
        <div class="relative flex-1" data-stepper-ignorar-enter @click.outside="resultados = []">
            <label for="{{ $campoRotulo }}-busca" class="form-label">Buscar município</label>
            <div class="relative mt-1.5">
                <i class="fas fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted"></i>
                <input
                    id="{{ $campoRotulo }}-busca"
                    type="search"
                    x-model="termo"
                    @keydown="teclarBusca($event)"
                    placeholder="Ex.: Rio Verde, Goiás"
                    autocomplete="off"
                    role="combobox"
                    aria-autocomplete="list"
                    :aria-expanded="resultados.length > 0"
                    aria-controls="{{ $campoRotulo }}-resultados"
                    class="form-control !pl-9"
                >
                <i x-show="buscando" x-cloak class="fas fa-spinner fa-spin absolute right-3 top-1/2 -translate-y-1/2 text-sm text-muted"></i>
            </div>

            <ul
                id="{{ $campoRotulo }}-resultados"
                x-show="resultados.length"
                x-cloak
                role="listbox"
                class="absolute z-[1200] mt-1 max-h-72 w-full overflow-auto rounded-xl border border-subtle bg-white py-1 shadow-xl dark:bg-gray-800"
            >
                <template x-for="(local, indice) in resultados" :key="`${local.latitude},${local.longitude}`">
                    <li
                        role="option"
                        :aria-selected="indice === destacado"
                        @click="escolher(local)"
                        @mouseenter="destacado = indice"
                        class="flex cursor-pointer items-center gap-3 px-4 py-2.5 text-sm"
                        :class="indice === destacado ? 'bg-emerald-50 dark:bg-emerald-900/30' : ''"
                    >
                        <i class="fas fa-location-dot text-emerald-600 dark:text-emerald-400"></i>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate font-medium text-heading" x-text="local.nome"></span>
                            <span class="block truncate text-xs text-muted" x-text="[local.estado, local.pais].filter(Boolean).join(' · ')"></span>
                        </span>
                    </li>
                </template>
            </ul>

            <p x-show="semResultado && !buscando" x-cloak class="mt-1.5 text-xs text-muted">
                Nenhum município encontrado. Tente sem acento ou marque o ponto no mapa.
            </p>
        </div>

        <button type="button" class="btn btn-secondary" @click="usarMinhaLocalizacao()" :disabled="localizando">
            <i class="fas" :class="localizando ? 'fa-spinner fa-spin' : 'fa-location-crosshairs'"></i>
            <span x-text="localizando ? 'Localizando...' : 'Usar minha localização'"></span>
        </button>
    </div>

    <div x-show="aviso" x-cloak class="flex items-start gap-2 rounded-lg bg-amber-50 px-3 py-2 text-sm text-amber-800 dark:bg-amber-900/20 dark:text-amber-200">
        <i class="fas fa-circle-info mt-0.5"></i>
        <span x-text="aviso"></span>
    </div>

    {{-- Mapa --}}
    <div class="overflow-hidden rounded-xl border border-subtle">
        <div x-ref="mapa" class="h-72 w-full bg-gray-100 dark:bg-gray-900 sm:h-80" aria-label="Mapa para marcar a localização"></div>

        <div class="flex flex-wrap items-center justify-between gap-2 border-t border-subtle bg-subtle px-4 py-2.5 text-xs">
            <span x-show="temPonto" class="flex items-center gap-2 text-heading">
                <i class="fas fa-location-dot text-emerald-600 dark:text-emerald-400"></i>
                <span class="font-readout" x-text="coordenadasTexto" data-resumo-rotulo="Coordenadas"></span>
            </span>
            <span x-show="!temPonto" class="text-muted">
                Clique no mapa para marcar o ponto da propriedade — ou use a busca.
            </span>
            <button type="button" x-show="temPonto" x-cloak @click="limpar()" class="font-medium text-muted hover:text-rose-600">
                <i class="fas fa-xmark"></i> Limpar ponto
            </button>
        </div>
    </div>

    {{-- Enviados com o formulário. O erro do servidor aparece logo abaixo (o modal.js
         insere a mensagem no pai do campo). --}}
    <div>
        <input type="hidden" name="{{ $campoLatitude }}" :value="latitude ?? ''">
        <input type="hidden" name="{{ $campoLongitude }}" :value="longitude ?? ''">
        @if ($errorLatitude)
            <div class="invalid-feedback">{{ $errorLatitude }}</div>
        @endif
    </div>

    <div class="flex flex-col gap-1.5">
        <label for="{{ $campoRotulo }}" class="form-label">
            <span>Nome do local</span>@if ($obrigatorio)<span class="ml-0.5 text-rose-500">*</span>@endif
        </label>
        <input
            id="{{ $campoRotulo }}"
            name="{{ $campoRotulo }}"
            type="text"
            x-ref="rotulo"
            x-model="rotulo"
            maxlength="500"
            autocomplete="off"
            placeholder="Ex.: Fazenda Boa Vista, Rio Verde - GO"
            @class(['form-control', 'is-invalid' => $errorRotulo])
        >
        @if ($errorRotulo)
            <div class="invalid-feedback">{{ $errorRotulo }}</div>
        @else
            <p class="text-xs text-muted">Preenchido pela busca; ajuste com o nome da fazenda ou uma referência.</p>
        @endif
    </div>
</div>
