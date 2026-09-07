{{-- Componente responsivo para exibir tabelas e cards --}}

@php
    use App\Utils\Util;
@endphp

{{-- Tabela: visível apenas em telas grandes --}}
<div class="mx-8 mb-8 mt-5 hidden overflow-hidden rounded-2xl shadow-[0_2px_6px_rgba(0,0,0,0.08)] md:block">
    <table class="w-full border-collapse bg-white text-left dark:bg-gray-800">
        <thead>
            <tr class="bg-subtle">
                @foreach ($arTableHead as $column)
                    <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $column['label'] }}</th>
                @endforeach

                @if($showActions)
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Ações</th>
                @endif
            </tr>
        </thead>

        <tbody>
            @foreach ($arValores as $item)
                <tr class="border-t border-gray-100 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/50">
                    @foreach ($arTableHead as $column)
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                            @php
                                $value = data_get($item, $column['key'], '-');
                                if (isset($column['function']) && method_exists(Util::class, $column['function'])) {
                                    $value = Util::{$column['function']}($value);
                                } elseif (isset($column['format']) && $column['format'] === 'date') {
                                    $value = Util::formatDate($value);
                                }
                            @endphp

                            {{-- Coluna booleana: exibida como selo colorido (true = vermelho, false = verde) --}}
                            @if(isset($column['badge']) && $column['badge'])
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $value ? 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' : 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' }}">
                                    {{ $value ? ($column['badgeTrueLabel'] ?? 'Sim') : ($column['badgeFalseLabel'] ?? 'Não') }}
                                </span>
                            {{-- Se for enum, mostra o label, senão mostra o valor normalmente --}}
                            @elseif(is_object($value) && method_exists($value, 'label'))
                                {{ $value->label() }}
                            @else
                                {{ $value ?? '-' }}
                            @endif
                        </td>
                    @endforeach
                    @if($showActions)
                        <td class="px-4 py-3 text-center">
                            @php
                                $acoes = [];
                                if (is_array($item) && isset($item['acoes'])) {
                                    $acoes = $item['acoes'];
                                } elseif (method_exists($item, 'setFuncionalidades')) {
                                    $acoes = $item->setFuncionalidades();
                                }
                                $acoesVisiveis = collect($acoes)->take(3);
                                $acoesExtras = collect($acoes)->slice(3)->values();
                            @endphp
                            <div class="inline-flex items-center gap-1" x-data="{ open: false, top: 0, left: 0, toggle(e) { const r = e.currentTarget.getBoundingClientRect(); this.top = r.bottom + window.scrollY + 4; this.left = r.right + window.scrollX - 192; this.open = !this.open; } }">
                                @foreach($acoesVisiveis as $acao)
                                    @if(isset($acao['link']))
                                        <a href="{{ $acao['link'] }}"
                                           class="inline-flex h-9 w-9 items-center justify-center rounded-full text-gray-500 transition-colors hover:bg-gray-100 hover:text-green-600 dark:text-gray-400 dark:hover:bg-gray-700"
                                           title="{{ $acao['nome'] }}"
                                           data-action="{{ $acao['id'] ?? 'show' }}">
                                            <i class="fa {{ $acao['icone'] }}"></i>
                                        </a>
                                    @else
                                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full text-gray-400 dark:text-gray-500" title="{{ $acao['nome'] }}">
                                            <i class="fa {{ $acao['icone'] }}"></i>
                                        </span>
                                    @endif
                                @endforeach

                                @if($acoesExtras->isNotEmpty())
                                    <button type="button" @click="toggle($event)"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-full text-gray-500 transition-colors hover:bg-gray-100 hover:text-green-600 dark:text-gray-400 dark:hover:bg-gray-700"
                                            title="Mais ações">
                                        <i class="fas fa-ellipsis-vertical"></i>
                                    </button>

                                    <template x-teleport="body">
                                        <div x-show="open" x-cloak @click.outside="open = false"
                                             x-transition
                                             :style="`top: ${top}px; left: ${left}px;`"
                                             class="fixed z-[1100] min-w-[192px] overflow-hidden rounded-xl border border-gray-200 bg-white py-1 text-left shadow-xl dark:border-gray-700 dark:bg-gray-800">
                                            @foreach($acoesExtras as $acao)
                                                @if(isset($acao['link']))
                                                    <a href="{{ $acao['link'] }}"
                                                       class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 transition-colors hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700"
                                                       data-action="{{ $acao['id'] ?? 'show' }}"
                                                       @click="open = false">
                                                        <i class="fa {{ $acao['icone'] }} w-4 text-center"></i>
                                                        {{ $acao['nome'] }}
                                                    </a>
                                                @else
                                                    <span class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-400 dark:text-gray-500">
                                                        <i class="fa {{ $acao['icone'] }} w-4 text-center"></i>
                                                        {{ $acao['nome'] }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </template>
                                @endif
                            </div>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


{{-- Cards: visíveis apenas em telas pequenas --}}
<div class="mx-8 mb-8 flex flex-col gap-5 md:hidden">
    @foreach ($arValores as $item)
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-[0_2px_8px_rgba(0,0,0,0.12)] transition-shadow hover:shadow-[0_8px_24px_rgba(0,0,0,0.16)] dark:border-gray-700 dark:bg-gray-800">
            <div class="flex flex-col gap-3">
                @foreach ($arTableHead as $column)
                    <div class="flex items-center justify-between border-b border-gray-100 py-1.5 last:border-b-0 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $column['label'] }}:</span>
                        <span class="text-sm text-muted">
                            @php
                                $value = data_get($item, $column['key'], '-');
                                if (isset($column['function']) && method_exists(Util::class, $column['function'])) {
                                    $value = Util::{$column['function']}($value);
                                }
                            @endphp
                            @if(isset($column['badge']) && $column['badge'])
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $value ? 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' : 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' }}">
                                    {{ $value ? ($column['badgeTrueLabel'] ?? 'Sim') : ($column['badgeFalseLabel'] ?? 'Não') }}
                                </span>
                            @elseif(is_object($value) && method_exists($value, 'label'))
                                {{ $value->label() }}
                            @else
                                {{ $value ?? '-' }}
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
            @if($showActions)
                @php
                    $acoes = [];
                    if (is_array($item) && isset($item['acoes'])) {
                        $acoes = $item['acoes'];
                    } elseif (method_exists($item, 'setFuncionalidades')) {
                        $acoes = $item->setFuncionalidades();
                    }
                    $acoesVisiveis = collect($acoes)->take(3);
                    $acoesExtras = collect($acoes)->slice(3)->values();
                @endphp
                <div class="mt-4 flex items-center gap-2" x-data="{ open: false, top: 0, left: 0, toggle(e) { const r = e.currentTarget.getBoundingClientRect(); this.top = r.bottom + window.scrollY + 4; this.left = r.right + window.scrollX - 192; this.open = !this.open; } }">
                    @foreach($acoesVisiveis as $acao)
                        @if(isset($acao['link']))
                            <a href="{{ $acao['link'] }}"
                               class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 text-gray-500 transition-colors hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600"
                               title="{{ $acao['nome'] }}"
                               data-action="{{ $acao['id'] ?? 'show' }}">
                                <i class="fa {{ $acao['icone'] }}"></i>
                            </a>
                        @else
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 text-gray-400 dark:bg-gray-700 dark:text-gray-500" title="{{ $acao['nome'] }}">
                                <i class="fa {{ $acao['icone'] }}"></i>
                            </span>
                        @endif
                    @endforeach

                    @if($acoesExtras->isNotEmpty())
                        <button type="button" @click="toggle($event)"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 text-gray-500 transition-colors hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600"
                                title="Mais ações">
                            <i class="fas fa-ellipsis-vertical"></i>
                        </button>

                        <template x-teleport="body">
                            <div x-show="open" x-cloak @click.outside="open = false"
                                 x-transition
                                 :style="`top: ${top}px; left: ${left}px;`"
                                 class="fixed z-[1100] min-w-[192px] overflow-hidden rounded-xl border border-gray-200 bg-white py-1 text-left shadow-xl dark:border-gray-700 dark:bg-gray-800">
                                @foreach($acoesExtras as $acao)
                                    @if(isset($acao['link']))
                                        <a href="{{ $acao['link'] }}"
                                           class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 transition-colors hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700"
                                           data-action="{{ $acao['id'] ?? 'show' }}"
                                           @click="open = false">
                                            <i class="fa {{ $acao['icone'] }} w-4 text-center"></i>
                                            {{ $acao['nome'] }}
                                        </a>
                                    @else
                                        <span class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-400 dark:text-gray-500">
                                            <i class="fa {{ $acao['icone'] }} w-4 text-center"></i>
                                            {{ $acao['nome'] }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </template>
                    @endif
                </div>
            @endif
        </div>
    @endforeach
</div>
