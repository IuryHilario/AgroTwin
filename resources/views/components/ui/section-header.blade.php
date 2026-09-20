{{--
    Cabeçalho das telas internas. O `modulo` dá a identidade visual (cor,
    ícone e rótulo) — a mesma na listagem, no cadastro e na edição, para o
    usuário reconhecer onde está pela cor antes de ler o título.

    Props:
      modulo    propriedades | lavouras | insumos | sensores | alertas | recomendacoes | conta
      title     título da tela
      etapa     telas de cadastro/edição: o rótulo superior vira "Módulo / Etapa";
                sem etapa (listagens), mostra o grupo do menu (Gestão, Monitoramento)
      subtitle  frase de apoio
      icon      sobrescreve o ícone do módulo
      stats     [['valor' => 2, 'rotulo' => 'ativas', 'destaque' => bool], ...]
      acao      ['route' => ..., 'text' => ..., 'icon' => ...] — ação principal
      buttons   [['route' => ..., 'text' => ..., 'icon' => ...], ...] — ações secundárias
--}}
@props([
    'title',
    'modulo' => 'conta',
    'etapa' => null,
    'subtitle' => null,
    'icon' => null,
    'stats' => [],
    'acao' => null,
    'buttons' => [],
])

@php
    $modulos = [
        'propriedades' => ['grupo' => 'Gestão', 'rotulo' => 'Propriedades', 'icone' => 'fa-map-location-dot', 'tom' => 'emerald'],
        'lavouras' => ['grupo' => 'Gestão', 'rotulo' => 'Lavouras', 'icone' => 'fa-seedling', 'tom' => 'lime'],
        'insumos' => ['grupo' => 'Gestão', 'rotulo' => 'Insumos', 'icone' => 'fa-flask', 'tom' => 'violet'],
        'sensores' => ['grupo' => 'Monitoramento', 'rotulo' => 'Sensores', 'icone' => 'fa-microchip', 'tom' => 'sky'],
        'alertas' => ['grupo' => 'Monitoramento', 'rotulo' => 'Alertas', 'icone' => 'fa-bell', 'tom' => 'amber'],
        'recomendacoes' => ['grupo' => 'Monitoramento', 'rotulo' => 'Recomendações', 'icone' => 'fa-lightbulb', 'tom' => 'teal'],
        'conta' => ['grupo' => 'Sua conta', 'rotulo' => 'Sua conta', 'icone' => 'fa-user-gear', 'tom' => 'slate'],
    ];
    $m = $modulos[$modulo] ?? $modulos['conta'];

    // Classes completas (e não montadas por concatenação) para o Tailwind encontrá-las no build.
    $tons = [
        'emerald' => ['barra' => 'bg-emerald-500', 'lavagem' => 'from-emerald-50 dark:from-emerald-500/10', 'tile' => 'bg-emerald-100 text-emerald-600 ring-emerald-500/20 dark:bg-emerald-500/15 dark:text-emerald-400', 'texto' => 'text-emerald-600 dark:text-emerald-400', 'marca' => 'text-emerald-500/[0.07] dark:text-emerald-400/[0.06]'],
        'lime' => ['barra' => 'bg-lime-500', 'lavagem' => 'from-lime-50 dark:from-lime-500/10', 'tile' => 'bg-lime-100 text-lime-700 ring-lime-500/20 dark:bg-lime-500/15 dark:text-lime-400', 'texto' => 'text-lime-700 dark:text-lime-400', 'marca' => 'text-lime-500/[0.08] dark:text-lime-400/[0.06]'],
        'violet' => ['barra' => 'bg-violet-500', 'lavagem' => 'from-violet-50 dark:from-violet-500/10', 'tile' => 'bg-violet-100 text-violet-600 ring-violet-500/20 dark:bg-violet-500/15 dark:text-violet-400', 'texto' => 'text-violet-600 dark:text-violet-400', 'marca' => 'text-violet-500/[0.07] dark:text-violet-400/[0.06]'],
        'sky' => ['barra' => 'bg-sky-500', 'lavagem' => 'from-sky-50 dark:from-sky-500/10', 'tile' => 'bg-sky-100 text-sky-600 ring-sky-500/20 dark:bg-sky-500/15 dark:text-sky-400', 'texto' => 'text-sky-600 dark:text-sky-400', 'marca' => 'text-sky-500/[0.07] dark:text-sky-400/[0.06]'],
        'amber' => ['barra' => 'bg-amber-500', 'lavagem' => 'from-amber-50 dark:from-amber-500/10', 'tile' => 'bg-amber-100 text-amber-600 ring-amber-500/20 dark:bg-amber-500/15 dark:text-amber-400', 'texto' => 'text-amber-600 dark:text-amber-400', 'marca' => 'text-amber-500/[0.08] dark:text-amber-400/[0.06]'],
        'teal' => ['barra' => 'bg-teal-500', 'lavagem' => 'from-teal-50 dark:from-teal-500/10', 'tile' => 'bg-teal-100 text-teal-600 ring-teal-500/20 dark:bg-teal-500/15 dark:text-teal-400', 'texto' => 'text-teal-600 dark:text-teal-400', 'marca' => 'text-teal-500/[0.07] dark:text-teal-400/[0.06]'],
        'slate' => ['barra' => 'bg-slate-500', 'lavagem' => 'from-slate-100 dark:from-slate-500/10', 'tile' => 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-500/15 dark:text-slate-300', 'texto' => 'text-slate-600 dark:text-slate-400', 'marca' => 'text-slate-500/[0.07] dark:text-slate-400/[0.06]'],
    ];
    $t = $tons[$m['tom']];
    $iconeFinal = $icon ?: 'fas ' . $m['icone'];
@endphp

<section class="relative mb-2 mt-1 overflow-hidden rounded-2xl border border-subtle bg-white shadow-sm dark:bg-gray-800">
    {{-- Identidade do módulo: faixa lateral, lavagem de cor e ícone em marca d'água --}}
    <span class="absolute inset-y-0 left-0 w-1 {{ $t['barra'] }}"></span>
    <span class="pointer-events-none absolute inset-0 bg-gradient-to-r {{ $t['lavagem'] }} via-transparent to-transparent"></span>
    <i class="{{ $iconeFinal }} pointer-events-none absolute -bottom-8 right-6 text-[9rem] leading-none {{ $t['marca'] }}"></i>

    <div class="relative flex flex-col gap-5 p-5 pl-6 md:p-6 md:pl-7 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex min-w-0 flex-1 items-center gap-4">
            <span class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl text-2xl shadow-sm ring-1 {{ $t['tile'] }}">
                <i class="{{ $iconeFinal }}"></i>
            </span>
            <div class="min-w-0">
                <p class="font-readout text-[11px] font-medium uppercase tracking-[0.18em] {{ $t['texto'] }}">
                    {{-- Listagem: grupo do menu · cadastro/edição: módulo / etapa --}}
                    @if ($etapa)
                        {{ $m['rotulo'] }}<span class="text-gray-400 dark:text-gray-500"> / {{ $etapa }}</span>
                    @else
                        {{ $m['grupo'] }}
                    @endif
                </p>
                <h1 class="mt-0.5 text-2xl font-semibold leading-tight text-heading">{{ $title }}</h1>
                @if ($subtitle)
                    <p class="mt-1 max-w-xl text-sm text-muted">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        @if (count($stats) || $acao || count($buttons))
            <div class="flex flex-shrink-0 flex-wrap items-center gap-3">
                @foreach ($stats as $stat)
                    <div class="min-w-[76px] rounded-xl border border-subtle bg-white/70 px-4 py-2 text-center backdrop-blur-sm dark:bg-gray-900/40">
                        <p class="font-readout text-xl font-semibold leading-tight {{ !empty($stat['destaque']) ? $t['texto'] : 'text-heading' }}">{{ $stat['valor'] }}</p>
                        <p class="text-[11px] uppercase tracking-wide text-muted">{{ $stat['rotulo'] }}</p>
                    </div>
                @endforeach

                @foreach ($buttons as $button)
                    <a href="{{ $button['route'] }}" class="btn btn-secondary">
                        @isset($button['icon'])<i class="{{ $button['icon'] }}"></i>@endisset
                        {{ $button['text'] }}
                    </a>
                @endforeach

                @if ($acao)
                    {{-- No mobile a ação principal fica no botão flutuante --}}
                    <a href="{{ $acao['route'] }}" class="btn btn-primary hidden sm:inline-flex">
                        <i class="{{ $acao['icon'] ?? 'fas fa-plus' }}"></i>
                        {{ $acao['text'] }}
                    </a>
                @endif
            </div>
        @endif
    </div>
</section>
