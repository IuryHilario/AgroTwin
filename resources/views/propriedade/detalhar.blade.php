<x-ui.modal-funcional
    modalId="detalharPropriedade"
    title="Detalhes da Propriedade"
    icon="fas fa-home"
    size="modal-lg"
    :item="$propriedade ?? null"
    resourceName="propriedade"
>
    @php $detalhes = $propriedade->getDetalhesFormatados(); @endphp

    <x-ui.detail-section title="Informações Básicas" icon="fas fa-info-circle">
        <x-ui.detail-fields :fields="[
            ['label' => 'Nome', 'value' => $detalhes['nome']],
            ['label' => 'Proprietário', 'value' => $detalhes['proprietario']],
            ['label' => 'Tipo de Solo', 'value' => $detalhes['tp_solo']],
            ['label' => 'Área Total (ha)', 'value' => $detalhes['area_hectares']],
            ['label' => 'Lavouras Cadastradas', 'value' => $detalhes['total_lavouras']],
            ['label' => 'Status', 'value' => $detalhes['status']],
        ]" />
    </x-ui.detail-section>

    <x-ui.detail-section title="Localização" icon="fas fa-map-marker-alt">
        <x-ui.detail-card>
            {{ $detalhes['localizacao'] ?: 'Não informada.' }}
            @if($propriedade->nu_latitude !== null && $propriedade->nu_longitude !== null)
                <span class="font-readout mt-1 block text-xs text-muted">
                    {{ number_format($propriedade->nu_latitude, 5, ',', '.') }}, {{ number_format($propriedade->nu_longitude, 5, ',', '.') }}
                </span>
            @else
                <span class="mt-1 block text-xs text-amber-600 dark:text-amber-400">
                    <i class="fas fa-triangle-exclamation mr-1"></i>Sem ponto no mapa — edite a propriedade para marcar a localização exata.
                </span>
            @endif
        </x-ui.detail-card>
    </x-ui.detail-section>

    <x-ui.detail-section title="Condições Meteorológicas" icon="fas fa-cloud-sun">
        @if($clima)
            @php $hoje = $clima['dias'][0] ?? null; @endphp
            <x-ui.detail-fields :fields="[
                ['label' => 'Agora', 'value' => $clima['temperatura'] . ' °C · ' . $clima['descricao']],
                ['label' => 'Sensação Térmica', 'value' => $clima['sensacao'] . ' °C'],
                ['label' => 'Chuva nas próximas 6h', 'value' => $clima['chuva_proximas_horas'] !== null ? $clima['chuva_proximas_horas'] . ' %' : '—'],
                ['label' => 'Chuva prevista hoje', 'value' => $hoje && $hoje['chuva_mm'] !== null ? number_format($hoje['chuva_mm'], 1, ',', '.') . ' mm' : '—'],
                ['label' => 'Umidade do Ar', 'value' => $clima['umidade'] . ' %'],
                ['label' => 'Vento', 'value' => $clima['vento_kmh'] . ' km/h'],
                ['label' => 'Evapotranspiração (ET0) hoje', 'value' => $hoje && $hoje['et0_mm'] !== null ? number_format($hoje['et0_mm'], 1, ',', '.') . ' mm' : '—'],
            ]" />

            @if(!empty($clima['dias']))
                <div class="mt-4 grid grid-cols-3 gap-2 sm:grid-cols-5">
                    @foreach($clima['dias'] as $dia)
                        <div class="flex flex-col items-center gap-1 rounded-lg bg-subtle px-2 py-3 text-center" title="{{ $dia['descricao'] }}">
                            <span class="text-xs font-medium text-heading">{{ $dia['rotulo'] }}</span>
                            <i class="fas {{ $dia['icone'] }} text-lg text-amber-500"></i>
                            <span class="font-readout text-xs text-heading">{{ $dia['maxima'] }}° <span class="text-muted">{{ $dia['minima'] }}°</span></span>
                            <span class="font-readout text-xs text-sky-600 dark:text-sky-400" title="Chance de chuva">
                                <i class="fas fa-droplet text-[10px]"></i> {{ $dia['chance_chuva'] ?? '—' }}%
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif

            <p class="mt-3 text-xs text-muted">Previsão para o ponto da propriedade.</p>
        @else
            <p class="text-sm text-muted">
                <i class="fas fa-circle-info mr-2"></i>
                Previsão indisponível agora — confira se a propriedade tem o ponto marcado no mapa.
            </p>
        @endif
    </x-ui.detail-section>
</x-ui.modal-funcional>
