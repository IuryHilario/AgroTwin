{{--
    Valor de uma coluna da listagem, formatado pelo 'tipo' da coluna:
      texto (padrão) · data · data_hora · numero (casas, sufixo) · status · booleano
      conexao (sensor: online/sem enviar) · validade (data + aviso de vencimento)
      estoque (quantidade + aviso de estoque abaixo do mínimo)
      episodio (alerta: nº de leituras fora da faixa e se ainda está em curso)
    'destaque' => true deixa o valor em evidência (use na coluna principal).
    Mesma renderização na tabela (desktop) e nos cards (mobile).
--}}
@props(['item', 'coluna'])

@php
    use App\Utils\Util;

    $bruto = data_get($item, $coluna['key']);
    $tipo = $coluna['tipo'] ?? 'texto';

    // Enum vira o próprio valor (para status) ou o label legível (para texto).
    $chave = $bruto instanceof \BackedEnum ? $bruto->value : $bruto;
    $rotuloEnum = is_object($bruto) && method_exists($bruto, 'label') ? $bruto->label() : null;

    $texto = match ($tipo) {
        'data' => Util::formatDate($bruto),
        'data_hora' => Util::formatDateTime($bruto),
        'numero' => ($n = Util::formatNumber($bruto, $coluna['casas'] ?? 2)) !== null
            ? trim($n . ' ' . ($coluna['sufixo'] ?? ''))
            : null,
        default => $rotuloEnum ?? (is_scalar($bruto) && $bruto !== '' ? (string) $bruto : null),
    };

    // Tom e rótulo dos status conhecidos do sistema (lavoura, sensor, alerta).
    $status = [
        'ativo' => ['ok', 'Ativo'],
        'colhida' => ['info', 'Colhida'],
        'encerrada' => ['neutro', 'Encerrada'],
        'inativo' => ['neutro', 'Inativo'],
        'critical' => ['erro', 'Crítico'],
        'warning' => ['alerta', 'Atenção'],
        'info' => ['info', 'Informativo'],
    ];
@endphp

@if ($tipo === 'episodio')
    {{-- Um alerta cobre todas as leituras fora da faixa até o valor normalizar. --}}
    <span class="inline-flex flex-col items-end gap-1">
        <x-ui.selo :tom="$item->emCurso() ? 'alerta' : 'neutro'">
            {{ $item->emCurso() ? 'Em curso' : 'Normalizado' }}
        </x-ui.selo>
        <span class="font-readout whitespace-nowrap text-xs text-muted">
            {{ $item->nu_ocorrencias }} {{ $item->nu_ocorrencias === 1 ? 'leitura' : 'leituras' }}
        </span>
    </span>

@elseif ($tipo === 'conexao')
    {{-- Saúde do sensor: o status cadastrado diz o que deveria ser, isto diz o que está acontecendo. --}}
    @php $ultima = $item->ultimaLeitura; @endphp
    @if (!$ultima)
        <x-ui.selo tom="neutro">Nunca enviou</x-ui.selo>
    @elseif ($item->estaOnline())
        <x-ui.selo tom="ok">{{ $ultima->dt_leitura->diffForHumans(['short' => true]) }}</x-ui.selo>
    @else
        <x-ui.selo tom="erro">Sem enviar há {{ $ultima->dt_leitura->diffForHumans(null, true) }}</x-ui.selo>
    @endif

@elseif ($tipo === 'validade')
    @if ($bruto === null)
        <span class="text-gray-300 dark:text-gray-600">—</span>
    @elseif ($item->vencido())
        <x-ui.selo tom="erro">Vencido em {{ Util::formatDate($bruto) }}</x-ui.selo>
    @elseif ($item->venceEmBreve())
        <x-ui.selo tom="alerta">Vence em {{ Util::formatDate($bruto) }}</x-ui.selo>
    @else
        <span class="font-readout whitespace-nowrap">{{ Util::formatDate($bruto) }}</span>
    @endif

@elseif ($tipo === 'estoque')
    @php $quantidade = trim(Util::formatNumber($bruto, $coluna['casas'] ?? 2) . ' ' . ($coluna['sufixo'] ?? '')); @endphp
    @if ($item->estoque_abaixo_minimo)
        <x-ui.selo tom="alerta">{{ $quantidade }} · abaixo do mínimo</x-ui.selo>
    @else
        <span class="font-readout whitespace-nowrap">{{ $quantidade }}</span>
    @endif

@elseif ($tipo === 'booleano')
    <x-ui.selo :tom="$bruto ? ($coluna['tomSim'] ?? 'neutro') : ($coluna['tomNao'] ?? 'ok')">
        {{ $bruto ? ($coluna['rotuloSim'] ?? 'Sim') : ($coluna['rotuloNao'] ?? 'Não') }}
    </x-ui.selo>
@elseif ($tipo === 'status' && $chave !== null && $chave !== '')
    @php [$tom, $rotulo] = $status[$chave] ?? ['neutro', $rotuloEnum ?? ucfirst((string) $chave)]; @endphp
    <x-ui.selo :tom="$tom">{{ $rotuloEnum ?? $rotulo }}</x-ui.selo>
@elseif ($texto === null)
    <span class="text-gray-300 dark:text-gray-600">—</span>
@elseif (!empty($coluna['destaque']))
    <span class="font-medium text-heading">{{ $texto }}</span>
@elseif (in_array($tipo, ['data', 'data_hora', 'numero'], true))
    <span class="font-readout whitespace-nowrap">{{ $texto }}</span>
@else
    {{ $texto }}
@endif
