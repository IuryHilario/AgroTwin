@extends('layouts.app')

@section('title', 'Configurações - AgroTwin')

@section('content')

    <x-ui.section-header
        title="Configurações"
        icon="fas fa-cog"
    />

    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 lg:col-span-6">
            <x-form.form
                action="{{ route('configuracoes.update') }}"
                method="PUT"
                title="Preferências"
            >
                <div class="col-span-12">
                    <label class="flex items-center gap-3 rounded-lg border-2 border-gray-200 p-4 dark:border-gray-600">
                        <input
                            type="checkbox"
                            name="fl_notificar_email_alerta"
                            value="1"
                            class="h-5 w-5 rounded border-gray-300 text-green-600 focus:ring-green-500"
                            {{ old('fl_notificar_email_alerta', $usuario->fl_notificar_email_alerta) ? 'checked' : '' }}
                        >
                        <span>
                            <span class="block font-medium text-gray-900 dark:text-gray-100">Notificar por e-mail</span>
                            <span class="block text-sm text-muted">Receber um e-mail sempre que um novo alerta for gerado em algum sensor.</span>
                        </span>
                    </label>
                </div>

                <div class="col-span-12">
                    <x-form.select
                        name="id_propriedade_padrao"
                        label="Propriedade padrão do Dashboard"
                        :options="['' => 'Sempre a primeira propriedade cadastrada'] + $propriedades->toArray()"
                        :value="old('id_propriedade_padrao', $usuario->id_propriedade_padrao ?? '')"
                        error="{{ $errors->first('id_propriedade_padrao') }}"
                    />
                </div>
            </x-form.form>
        </div>

        <div class="col-span-12 lg:col-span-6">
            <div class="card p-5">
                <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-muted">
                    <i class="fas fa-lightbulb text-amber-500"></i>
                    Em breve
                </h3>
                <ul class="list-disc space-y-2 pl-5 text-sm text-gray-600 dark:text-gray-400">
                    <li>Exportar leituras e relatórios em CSV/PDF</li>
                    <li>Token de API pessoal para integrações externas</li>
                    <li>Unidade de medida preferida (°C/°F, ha/m²)</li>
                    <li>Notificação por Telegram/WhatsApp além do e-mail</li>
                    <li>Excluir conta e todos os dados</li>
                </ul>
            </div>
        </div>
    </div>

@endsection
