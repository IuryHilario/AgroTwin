{{--
    Ações de uma linha da listagem. As 3 primeiras ficam visíveis; o resto vai
    para o menu "Mais ações". As ações vêm de $item->setFuncionalidades().
    variante: 'tabela' (botões redondos) | 'card' (botões quadrados do mobile)
--}}
@props(['item', 'variante' => 'tabela'])

@php
    $acoes = collect(match (true) {
        is_array($item) && isset($item['acoes']) => $item['acoes'],
        is_object($item) && method_exists($item, 'setFuncionalidades') => $item->setFuncionalidades(),
        default => [],
    });

    $visiveis = $acoes->take(3);
    $extras = $acoes->slice(3)->values();

    $botao = $variante === 'card'
        ? 'rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600'
        : 'rounded-full hover:bg-gray-100 dark:hover:bg-gray-700';
@endphp

<div {{ $attributes->class('inline-flex items-center gap-1') }}
     x-data="{ open: false, top: 0, left: 0, toggle(e) { const r = e.currentTarget.getBoundingClientRect(); this.top = r.bottom + window.scrollY + 4; this.left = r.right + window.scrollX - 192; this.open = !this.open; } }">

    @foreach ($visiveis as $acao)
        @if (isset($acao['link']))
            <a href="{{ $acao['link'] }}" title="{{ $acao['nome'] }}" data-action="{{ $acao['id'] ?? 'show' }}"
               class="inline-flex h-9 w-9 items-center justify-center text-gray-500 transition-colors hover:text-green-600 dark:text-gray-400 {{ $botao }}">
                <i class="fa {{ $acao['icone'] }}"></i>
            </a>
        @else
            <span title="{{ $acao['nome'] }}" class="inline-flex h-9 w-9 items-center justify-center text-gray-300 dark:text-gray-600">
                <i class="fa {{ $acao['icone'] }}"></i>
            </span>
        @endif
    @endforeach

    @if ($extras->isNotEmpty())
        <button type="button" @click="toggle($event)" title="Mais ações"
                class="inline-flex h-9 w-9 items-center justify-center text-gray-500 transition-colors hover:text-green-600 dark:text-gray-400 {{ $botao }}">
            <i class="fas fa-ellipsis-vertical"></i>
        </button>

        <template x-teleport="body">
            <div x-show="open" x-cloak x-transition @click.outside="open = false"
                 :style="`top: ${top}px; left: ${left}px;`"
                 class="fixed z-[1100] min-w-[192px] overflow-hidden rounded-xl border border-gray-200 bg-white py-1 text-left shadow-xl dark:border-gray-700 dark:bg-gray-800">
                @foreach ($extras as $acao)
                    @if (isset($acao['link']))
                        <a href="{{ $acao['link'] }}" data-action="{{ $acao['id'] ?? 'show' }}" @click="open = false"
                           class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 transition-colors hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700">
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
