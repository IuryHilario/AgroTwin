{{--
    Revisão do que foi preenchido nas etapas anteriores. Usar dentro de um
    <x-ui.etapa> de um <x-ui.stepper>: lê o `resumo` do stepper, que se monta
    sozinho a partir dos campos com nome e rótulo de cada etapa.
    Botão "Alterar" volta direto para a etapa.
--}}
<div {{ $attributes->class('grid grid-cols-1 gap-4 lg:grid-cols-2') }}>
    <template x-for="bloco in resumo" :key="bloco.etapa">
        <section class="rounded-xl border border-subtle bg-subtle p-4">
            <header class="mb-3 flex items-center justify-between gap-3">
                <h3 class="flex items-center gap-2 text-sm font-semibold text-heading">
                    <span class="font-readout flex h-6 w-6 items-center justify-center rounded-full bg-emerald-600 text-xs text-white" x-text="bloco.etapa"></span>
                    <span x-text="bloco.titulo"></span>
                </h3>
                <button type="button" @click="irPara(bloco.etapa)" class="text-sm font-medium text-emerald-700 hover:underline dark:text-emerald-400">
                    <i class="fas fa-pen text-xs"></i> Alterar
                </button>
            </header>

            <dl class="divide-y divide-gray-200/70 dark:divide-gray-700">
                <template x-for="campo in bloco.campos" :key="campo.rotulo">
                    <div class="flex items-start justify-between gap-4 py-2 text-sm">
                        <dt class="text-muted" x-text="campo.rotulo"></dt>
                        <dd class="text-right font-medium" :class="campo.valor ? 'text-heading' : 'text-gray-400 dark:text-gray-500'" x-text="campo.valor ?? 'Não informado'"></dd>
                    </div>
                </template>
            </dl>
        </section>
    </template>
</div>
