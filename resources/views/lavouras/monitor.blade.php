<x-ui.modal-funcional
    modalId="monitorLavoura"
    title="Monitoramento"
    icon="fas fa-desktop"
    bgColor="bg-dark"
    size="modal-xl"
    :item="$lavoura ?? null"
    resourceName="lavoura"
    :additionalButtons="$lavoura ? [
        $lavoura->fl_irrigacao_ativa
            ? ['tag' => 'a', 'text' => 'Parar Irrigação', 'class' => 'btn-danger', 'icon' => 'fas fa-stop', 'href' => route('lavouras.irrigacao.parar', $lavoura->id_lavoura)]
            : ['tag' => 'a', 'text' => 'Irrigar Agora', 'class' => 'btn-info', 'icon' => 'fas fa-tint', 'href' => route('lavouras.irrigacao.iniciar', $lavoura->id_lavoura)],
    ] : []"
>
    @if($lavoura)
        <!-- Resumo da Lavoura -->
        <div class="mb-4 grid grid-cols-12 gap-4">
            <div class="card col-span-12 bg-subtle">
                <div class="p-4">
                    <h6 class="mb-3 flex items-center text-base font-semibold">
                        <i class="fas fa-seedling mr-2 text-green-600"></i>
                        Informações da Lavoura
                    </h6>
                    <div class="grid grid-cols-12 gap-4 text-sm">
                        <div class="col-span-12 md:col-span-3">
                            <strong>Cultura:</strong> {{ $lavoura->ds_cultura ?? 'Não informada' }}
                        </div>
                        <div class="col-span-12 md:col-span-3">
                            <strong>Status:</strong>
                            <span class="rounded-full bg-green-600 px-2 py-1 text-xs font-semibold text-white">{{ $lavoura->tp_status ? $lavoura->tp_status->label() : 'Ativo' }}</span>
                        </div>
                        <div class="col-span-12 md:col-span-3">
                            <strong>Plantio:</strong> {{ $lavoura->dt_plantio ? \Carbon\Carbon::parse($lavoura->dt_plantio)->format('d/m/Y') : 'Não informado' }}
                        </div>
                        <div class="col-span-12 md:col-span-3">
                            <strong>Colheita:</strong> {{ $lavoura->dt_colheita ? \Carbon\Carbon::parse($lavoura->dt_colheita)->format('d/m/Y') : 'Não informado' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status de Irrigação -->
        <div class="mb-4 grid grid-cols-12 gap-4">
            <div class="card col-span-12 border-2 {{ $lavoura->fl_irrigacao_ativa ? 'border-sky-500' : 'border-gray-300 dark:border-gray-600' }}">
                <div class="p-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-faucet-drip text-2xl {{ $lavoura->fl_irrigacao_ativa ? 'text-sky-500' : 'text-muted' }}"></i>
                            <div>
                                <h6 class="font-semibold">Irrigação</h6>
                                <span class="rounded-full {{ $lavoura->fl_irrigacao_ativa ? 'bg-sky-500' : 'bg-gray-400' }} px-2 py-1 text-xs font-semibold text-white">
                                    {{ $lavoura->fl_irrigacao_ativa ? 'Ativa' : 'Inativa' }}
                                </span>
                            </div>
                        </div>
                        <div class="text-sm text-muted">
                            <div><strong>Intervalo de leitura:</strong> {{ $lavoura->nu_intervalo_leitura_minutos }} min</div>
                            <div><strong>Token de irrigação:</strong> <code class="text-xs">{{ $lavoura->token_irrigacao ?? 'Não gerado' }}</code></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sensores da Lavoura -->
        <div class="mb-4">
            <h6 class="mb-3 flex items-center font-semibold">
                <i class="fas fa-satellite-dish mr-2"></i>
                Sensores da Lavoura
            </h6>
            <div class="overflow-x-auto rounded-xl shadow-[0_2px_10px_rgba(0,0,0,0.08)]">
                <table class="w-full border-collapse text-left text-sm">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="px-4 py-3">Sensor</th>
                            <th class="px-4 py-3">Tipo</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Última Leitura</th>
                            <th class="px-4 py-3">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lavoura->sensores as $sensor)
                            @php $leitura = $ultimasLeituras[$sensor->id_sensor] ?? null; @endphp
                            <tr class="border-t border-subtle even:bg-gray-50 dark:even:bg-gray-900/40 hover:bg-gray-100 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3">{{ $sensor->ds_nome }}</td>
                                <td class="px-4 py-3">{{ $sensor->tp_sensor?->label() ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full {{ $sensor->ds_status?->value === 'ativo' ? 'bg-green-600' : 'bg-red-600' }} px-2 py-1 text-xs font-semibold text-white">
                                        {{ $sensor->ds_status?->label() ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $leitura ? $leitura->dt_leitura->format('d/m/Y H:i:s') : '-' }}</td>
                                <td class="px-4 py-3">{{ $leitura ? $leitura->valor . $sensor->tp_sensor?->unidade() : 'Sem leitura' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-muted">Nenhum sensor cadastrado nesta lavoura.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recomendações e Histórico de Irrigação -->
        <div class="mb-4 grid grid-cols-12 gap-4">
            <div class="card col-span-12 md:col-span-6">
                <div class="rounded-t-2xl bg-amber-400 px-4 py-3 text-gray-900">
                    <h6 class="flex items-center font-semibold">
                        <i class="fas fa-lightbulb mr-2"></i>
                        Recomendações Recentes
                    </h6>
                </div>
                <div class="p-4">
                    @forelse($recomendacoes as $recomendacao)
                        <div class="mb-3 border-b border-subtle pb-3 text-sm last:mb-0 last:border-0 last:pb-0">
                            <p class="m-0">{{ $recomendacao->ds_recomendacao }}</p>
                            <small class="text-muted">{{ $recomendacao->dt_recomendacao->format('d/m/Y H:i') }}</small>
                        </div>
                    @empty
                        <p class="m-0 text-sm text-muted">Nenhuma recomendação registrada ainda.</p>
                    @endforelse
                </div>
            </div>

            <div class="card col-span-12 md:col-span-6">
                <div class="rounded-t-2xl bg-sky-500 px-4 py-3 text-white">
                    <h6 class="flex items-center font-semibold">
                        <i class="fas fa-history mr-2"></i>
                        Histórico de Irrigação
                    </h6>
                </div>
                <div class="p-4">
                    @forelse($historicoIrrigacao as $irrigacao)
                        <div class="mb-3 border-b border-subtle pb-3 text-sm last:mb-0 last:border-0 last:pb-0">
                            <div class="flex items-center justify-between">
                                <span class="font-medium">{{ $irrigacao->tp_acionamento === 'manual' ? 'Manual' : 'Automática' }}</span>
                                <span class="text-muted">{{ $irrigacao->getDuracaoFormatada() ?? 'Em andamento' }}</span>
                            </div>
                            <small class="text-muted">{{ $irrigacao->dt_inicio->format('d/m/Y H:i') }}</small>
                            @if($irrigacao->ds_motivo)
                                <p class="m-0 mt-1 text-muted">{{ $irrigacao->ds_motivo }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="m-0 text-sm text-muted">Nenhuma irrigação registrada ainda.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</x-ui.modal-funcional>
