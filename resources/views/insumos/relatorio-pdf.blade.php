<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Helvetica, Arial, sans-serif; color: #1f2937; font-size: 12px; }
        h1 { font-size: 18px; margin-bottom: 2px; }
        .subtitulo { color: #6b7280; margin-bottom: 20px; }
        .header { background: #16a34a; color: #fff; padding: 14px 18px; border-radius: 6px; margin-bottom: 18px; }
        .header h1 { color: #fff; }
        .header p { margin: 2px 0 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; font-size: 11px; }
        th { background: #f3f4f6; }
        .kpis { width: 100%; margin-bottom: 20px; }
        .kpis td { border: none; padding: 0 10px 0 0; }
        .kpi-box { border: 1px solid #d1d5db; border-radius: 6px; padding: 10px; text-align: center; }
        .kpi-box .valor { font-size: 16px; font-weight: bold; }
        .kpi-box .label { color: #6b7280; font-size: 10px; }
        h2 { font-size: 14px; margin-top: 20px; margin-bottom: 8px; border-bottom: 2px solid #16a34a; padding-bottom: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $insumo->ds_nome }}</h1>
        <p>Relatório de Consumo e Custo — Últimos {{ $relatorio['periodoDias'] }} dias</p>
        <p>Gerado em {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    @php $unidade = $insumo->tp_unidade_medida ? $insumo->tp_unidade_medida->value : 'UN'; @endphp

    <table class="kpis">
        <tr>
            <td width="25%">
                <div class="kpi-box">
                    <div class="valor">{{ number_format($relatorio['consumoTotal'], 2, ',', '.') }} {{ $unidade }}</div>
                    <div class="label">CONSUMO TOTAL</div>
                </div>
            </td>
            <td width="25%">
                <div class="kpi-box">
                    <div class="valor">R$ {{ number_format($relatorio['custoTotal'], 2, ',', '.') }}</div>
                    <div class="label">CUSTO ESTIMADO</div>
                </div>
            </td>
            <td width="25%">
                <div class="kpi-box">
                    <div class="valor">{{ number_format($relatorio['areaTratada'], 2, ',', '.') }} ha</div>
                    <div class="label">ÁREA TRATADA</div>
                </div>
            </td>
            <td width="25%">
                <div class="kpi-box">
                    <div class="valor">{{ $relatorio['totalAplicacoes'] }}</div>
                    <div class="label">APLICAÇÕES</div>
                </div>
            </td>
        </tr>
    </table>

    <h2>Distribuição por Lavoura</h2>
    <table>
        <thead>
            <tr><th>Lavoura</th><th>Quantidade</th><th>% do total</th></tr>
        </thead>
        <tbody>
            @forelse ($relatorio['distribuicaoPorLavoura'] as $lavoura)
                <tr>
                    <td>{{ $lavoura['nome'] }}</td>
                    <td>{{ number_format($lavoura['quantidade'], 2, ',', '.') }} {{ $unidade }}</td>
                    <td>{{ $lavoura['percentual'] }}%</td>
                </tr>
            @empty
                <tr><td colspan="3">Nenhuma aplicação registrada no período.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Análise Detalhada por Semana</h2>
    <table>
        <thead>
            <tr><th>Período</th><th>Aplicações</th><th>Quantidade</th><th>Área</th><th>Custo Estimado</th></tr>
        </thead>
        <tbody>
            @foreach ($relatorio['serieSemanal'] as $semana)
                <tr>
                    <td>{{ $semana['label'] }}</td>
                    <td>{{ $semana['aplicacoes'] }}</td>
                    <td>{{ number_format($semana['quantidade'], 2, ',', '.') }} {{ $unidade }}</td>
                    <td>{{ number_format($semana['area'], 2, ',', '.') }} ha</td>
                    <td>R$ {{ number_format($semana['custo'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
