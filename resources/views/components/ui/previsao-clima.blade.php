{{--
    Prévia do tempo (Open-Meteo) no ponto marcado no formulário. Lê a latitude e
    a longitude dos campos do próprio <form> — normalmente preenchidos por um
    <x-form.localidade>. Comportamento em resources/js/componentes/previsao-clima.js.
--}}
@props([
    'campoLatitude' => 'nu_latitude',
    'campoLongitude' => 'nu_longitude',
])

<div
    x-data="previsaoClima({{ Js::from(['url' => route('localidades.clima'), 'campoLatitude' => $campoLatitude, 'campoLongitude' => $campoLongitude]) }})"
    data-resumo-ignorar
    {{ $attributes->class('rounded-xl border border-subtle p-4 md:p-5') }}
>
    <div class="mb-4 flex items-center justify-between gap-3">
        <h3 class="flex items-center gap-2 text-sm font-semibold text-heading">
            <i class="fas fa-cloud-sun text-sky-500"></i> Tempo neste ponto
        </h3>
    </div>

    {{-- Carregando --}}
    <div x-show="carregando" class="flex animate-pulse flex-col gap-3">
        <div class="h-10 w-2/3 rounded-lg bg-gray-200 dark:bg-gray-700"></div>
        <div class="grid grid-cols-5 gap-2">
            @for ($i = 0; $i < 5; $i++)
                <div class="h-20 rounded-lg bg-gray-200 dark:bg-gray-700"></div>
            @endfor
        </div>
    </div>

    <p x-show="!carregando && semPonto" x-cloak class="text-sm text-muted">
        <i class="fas fa-circle-info mr-1"></i> Marque a localização na etapa anterior para ver a previsão.
    </p>

    <p x-show="!carregando && !semPonto && falhou" x-cloak class="text-sm text-muted">
        <i class="fas fa-triangle-exclamation mr-1 text-amber-500"></i>
        Previsão indisponível agora. Isso não impede o cadastro.
    </p>

    <template x-if="!carregando && clima">
        <div class="flex flex-col gap-4">
            {{-- Agora --}}
            <div class="flex flex-wrap items-center gap-x-6 gap-y-3">
                <div class="flex items-center gap-3">
                    <i class="fas text-3xl text-amber-500" :class="clima.icone"></i>
                    <div>
                        <p class="font-readout text-2xl font-semibold text-heading"><span x-text="clima.temperatura"></span>°C</p>
                        <p class="text-sm text-muted" x-text="clima.descricao"></p>
                    </div>
                </div>

                <dl class="grid grid-cols-3 gap-x-5 gap-y-1 text-sm">
                    <div>
                        <dt class="text-xs text-muted">Chuva (6h)</dt>
                        <dd class="font-readout font-semibold text-sky-600 dark:text-sky-400"><span x-text="numero(clima.chuva_proximas_horas)"></span>%</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted">Umidade do ar</dt>
                        <dd class="font-readout font-semibold text-heading"><span x-text="clima.umidade"></span>%</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted">Vento</dt>
                        <dd class="font-readout font-semibold text-heading"><span x-text="clima.vento_kmh"></span> km/h</dd>
                    </div>
                </dl>
            </div>

            {{-- Próximos dias --}}
            <div class="grid grid-cols-3 gap-2 sm:grid-cols-5">
                <template x-for="dia in clima.dias" :key="dia.data">
                    <div class="flex flex-col items-center gap-1 rounded-lg bg-subtle px-2 py-3 text-center" :title="dia.descricao">
                        <span class="text-xs font-medium text-heading" x-text="dia.rotulo"></span>
                        <i class="fas text-lg text-amber-500" :class="dia.icone"></i>
                        <span class="font-readout text-xs text-heading">
                            <span x-text="dia.maxima"></span>° <span class="text-muted" x-text="dia.minima + '°'"></span>
                        </span>
                        <span class="font-readout text-xs text-sky-600 dark:text-sky-400" title="Chance de chuva">
                            <i class="fas fa-droplet text-[10px]"></i> <span x-text="numero(dia.chance_chuva)"></span>%
                        </span>
                    </div>
                </template>
            </div>
        </div>
    </template>
</div>
