<x-ui.modal-funcional
    modalId="detalharInsumo"
    title="Detalhes do Insumo"
    icon="fas fa-info-circle"
    size="modal-lg"
    :item="$insumo ?? null"
    resourceName="insumo"
>
    @php
        $detalhes = $insumo->getDetalhesFormatados();

        $validadeTexto = 'Não informada';
        if ($detalhes['data_validade']) {
            $validade = \Carbon\Carbon::parse($detalhes['data_validade']);
            $diasRestantes = (int) \Carbon\Carbon::now()->diffInDays($validade, false);
            $validadeTexto = $validade->format('d/m/Y') . ' — ';
            $validadeTexto .= match(true) {
                $diasRestantes < 0 => 'Vencido',
                $diasRestantes <= 30 => "Vence em {$diasRestantes} dias",
                default => 'Válido',
            };
        }
    @endphp

    <x-ui.detail-section title="Informações Principais" icon="fas fa-info-circle">
        <x-ui.detail-fields :fields="[
            ['label' => 'Nome', 'value' => $detalhes['nome']],
            ['label' => 'Tipo', 'value' => $detalhes['tipo']],
            ['label' => 'Fabricante', 'value' => $detalhes['fabricante']],
            ['label' => 'Unidade de Medida', 'value' => $detalhes['unidade_medida']],
            ['label' => 'Data de Validade', 'value' => $validadeTexto],
            ['label' => 'Usuário Responsável', 'value' => $detalhes['usuario']],
        ]" />
    </x-ui.detail-section>

    <x-ui.detail-section title="Composição" icon="fas fa-atom">
        <x-ui.detail-card>{{ $detalhes['composicao'] ?: 'Não informada.' }}</x-ui.detail-card>
    </x-ui.detail-section>
</x-ui.modal-funcional>
