{{--
    Listagem responsiva: tabela no desktop, cards no mobile.
    Cada coluna: ['label' => ..., 'key' => ..., 'tipo' => ...] — os tipos e
    opções ficam documentados em <x-ui.celula>.

    Quando $arValores é um paginador, o rodapé de páginas aparece sozinho.
    Passando :busca (string, mesmo vazia) a barra de busca também aparece, e o
    estado vazio muda de "nada cadastrado" para "nada encontrado".
    Use o slot :filtros para controles extras ao lado da busca.
--}}
@props([
    'arTableHead' => [],
    'arValores' => [],
    'showActions' => false,
    'busca' => null,
    'buscaPlaceholder' => 'Buscar...',
    'vazioIcone' => 'fa-folder-open',
    'vazioTitulo' => 'Nada cadastrado ainda',
    'vazioTexto' => null,
    'vazioRota' => null,
    'vazioAcao' => null,
    'filtros' => null,
])

@php
    // Aceita tanto showActions="true" (string) quanto :showActions="true".
    $comAcoes = filter_var($showActions, FILTER_VALIDATE_BOOLEAN);
    $paginador = $arValores instanceof \Illuminate\Contracts\Pagination\Paginator ? $arValores : null;
    $buscando = $busca !== null && $busca !== '';
    $comBarra = $busca !== null || $filtros !== null;
@endphp

@if ($comBarra)
    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        @if ($busca !== null)
            <x-ui.busca :valor="$busca" :placeholder="$buscaPlaceholder" />
        @else
            <span></span>
        @endif

        @if ($filtros)
            <div class="flex flex-wrap items-center gap-2">{{ $filtros }}</div>
        @endif
    </div>
@endif

@if (count($arValores) === 0)

    <div class="mb-8 mt-5 rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-14 text-center dark:border-gray-700 dark:bg-gray-800">
        <span class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-green-50 text-2xl text-green-600 dark:bg-green-900/30 dark:text-green-400">
            <i class="fas {{ $buscando ? 'fa-magnifying-glass' : $vazioIcone }}"></i>
        </span>

        @if ($buscando)
            <p class="text-base font-semibold text-heading">Nenhum resultado para “{{ $busca }}”</p>
            <p class="mx-auto mt-1.5 max-w-sm text-sm text-muted">Tente outro termo ou limpe a busca para ver a lista completa.</p>
        @else
            <p class="text-base font-semibold text-heading">{{ $vazioTitulo }}</p>
            @if ($vazioTexto)
                <p class="mx-auto mt-1.5 max-w-sm text-sm text-muted">{{ $vazioTexto }}</p>
            @endif
            @if ($vazioRota)
                <a href="{{ $vazioRota }}" class="btn btn-primary mt-6">
                    <i class="fas fa-plus"></i>
                    {{ $vazioAcao ?? 'Cadastrar' }}
                </a>
            @endif
        @endif
    </div>

@else

    {{-- Desktop --}}
    <div class="mt-5 hidden overflow-x-auto rounded-2xl border border-subtle bg-white shadow-sm md:block dark:bg-gray-800">
        <table class="w-full border-collapse text-left">
            <thead>
                <tr class="border-b border-subtle bg-subtle">
                    @foreach ($arTableHead as $coluna)
                        <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-muted">{{ $coluna['label'] }}</th>
                    @endforeach
                    @if ($comAcoes)
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-muted">Ações</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach ($arValores as $item)
                    <tr class="transition-colors hover-surface">
                        @foreach ($arTableHead as $coluna)
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                <x-ui.celula :item="$item" :coluna="$coluna" />
                            </td>
                        @endforeach
                        @if ($comAcoes)
                            <td class="px-4 py-2 text-right">
                                <x-ui.acoes-linha :item="$item" />
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile --}}
    <div class="mt-5 flex flex-col gap-3 md:hidden">
        @foreach ($arValores as $item)
            <div class="rounded-2xl border border-subtle bg-white p-4 shadow-sm dark:bg-gray-800">
                <dl class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($arTableHead as $coluna)
                        <div class="flex items-center justify-between gap-4 py-2 first:pt-0 last:pb-0">
                            <dt class="text-xs font-medium uppercase tracking-wide text-muted">{{ $coluna['label'] }}</dt>
                            <dd class="text-right text-sm text-gray-700 dark:text-gray-300">
                                <x-ui.celula :item="$item" :coluna="$coluna" />
                            </dd>
                        </div>
                    @endforeach
                </dl>
                @if ($comAcoes)
                    <x-ui.acoes-linha :item="$item" variante="card" class="mt-3 w-full border-t border-subtle pt-3" />
                @endif
            </div>
        @endforeach
    </div>

    @if ($paginador)
        <x-ui.paginacao :paginador="$paginador" />
    @endif

    <div class="mb-8"></div>

@endif
