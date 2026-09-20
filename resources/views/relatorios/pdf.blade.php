<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Helvetica, Arial, sans-serif; color: #1f2937; font-size: 11px; }
        .cabecalho { background: #0f1f1a; color: #fff; padding: 16px 18px; border-radius: 6px; margin-bottom: 18px; }
        .cabecalho .rotulo { color: #6ee7b7; font-size: 9px; letter-spacing: 2px; text-transform: uppercase; }
        .cabecalho h1 { font-size: 18px; margin: 4px 0 2px; }
        .cabecalho p { margin: 0; color: #cbd5e1; }
        .veredito { border: 1px solid #d1d5db; border-left: 4px solid #6b7280; border-radius: 6px; padding: 12px 14px; margin-bottom: 14px; }
        .veredito.critico { border-left-color: #f43f5e; }
        .veredito.atencao { border-left-color: #f59e0b; }
        .veredito.ideal { border-left-color: #10b981; }
        .veredito h2 { margin: 0 0 4px; border: 0; padding: 0; font-size: 13px; }
        .veredito p { margin: 0; color: #4b5563; }
        .indice { float: right; font-family: 'DejaVu Sans Mono', monospace; font-size: 22px; font-weight: bold; }
        .indice small { display: block; font-size: 8px; font-weight: normal; letter-spacing: 1px; text-transform: uppercase; color: #6b7280; }
        .kpis { width: 100%; margin-bottom: 18px; border-collapse: separate; border-spacing: 6px 0; }
        .kpi { border: 1px solid #d1d5db; border-radius: 6px; padding: 10px; text-align: center; }
        .kpi .valor { font-size: 16px; font-weight: bold; font-family: 'DejaVu Sans Mono', monospace; }
        .kpi .label { color: #6b7280; font-size: 9px; text-transform: uppercase; }
        h2 { font-size: 13px; margin: 18px 0 8px; border-bottom: 2px solid #16a34a; padding-bottom: 4px; }
        table.dados { width: 100%; border-collapse: collapse; }
        table.dados th, table.dados td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; }
        table.dados th { background: #f3f4f6; font-size: 9px; text-transform: uppercase; color: #4b5563; }
        .num { font-family: 'DejaVu Sans Mono', monospace; text-align: right; }
        .situacao { font-weight: bold; font-size: 9px; text-transform: uppercase; }
        .situacao.critico { color: #be123c; }
        .situacao.atencao { color: #b45309; }
        .situacao.ideal { color: #047857; }
        .situacao.sem_limite, .situacao.sem_dados { color: #6b7280; }
        ol.acoes { margin: 0; padding-left: 16px; }
        ol.acoes li { margin-bottom: 6px; }
        ol.acoes strong { display: block; }
        .vazio { color: #9ca3af; }
        .rodape { margin-top: 24px; color: #9ca3af; font-size: 9px; text-align: center; }
    </style>
</head>
<body>
    @php
        $geral = $diagnostico['situacaoGeral'];
        $rotulos = [
            'critico' => 'Precisa de ação',
            'atencao' => 'Atenção',
            'ideal' => 'Dentro do ideal',
            'sem_limite' => 'Sem faixa ideal',
            'sem_dados' => 'Sem leituras',
        ];
    @endphp

    <div class="cabecalho">
        <div class="rotulo">Diagnóstico do solo</div>
        <h1>{{ $selectedPropriedade->ds_nome }}@if($selectedLavoura) — {{ $selectedLavoura->ds_cultura }}@endif</h1>
        <p>Período: {{ $inicio->format('d/m/Y') }} a {{ $fim->format('d/m/Y') }} ({{ $periodoDias }} dias) · Gerado em {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="veredito {{ $geral['nivel'] }}">
        @if ($diagnostico['indice'] !== null)
            <div class="indice">{{ $diagnostico['indice'] }}%<small>no ideal</small></div>
        @endif
        <h2>{{ $geral['titulo'] }}</h2>
        <p>{{ $geral['texto'] }}</p>
    </div>

    <table class="kpis">
        <tr>
            <td class="kpi"><div class="valor">{{ $kpis['totalLeituras'] }}</div><div class="label">Leituras</div></td>
            <td class="kpi"><div class="valor">{{ $kpis['sensoresAtivos'] }}/{{ $kpis['totalSensores'] }}</div><div class="label">Sensores ativos</div></td>
            <td class="kpi"><div class="valor">{{ $kpis['totalAlertas'] }}</div><div class="label">Alertas</div></td>
            <td class="kpi"><div class="valor">{{ $geral['criticos'] + $geral['atencao'] }}</div><div class="label">Fora do ideal</div></td>
        </tr>
    </table>

    <h2>O que fazer agora</h2>
    @if ($diagnostico['acoes']->isNotEmpty())
        <ol class="acoes">
            @foreach ($diagnostico['acoes'] as $acao)
                <li>
                    <strong>{{ $acao['rotulo'] }} — {{ $rotulos[$acao['situacao']] }}</strong>
                    {{ $acao['texto'] }}
                </li>
            @endforeach
        </ol>
    @else
        <p class="vazio">Nada exige ação no período. Siga o manejo atual e acompanhe os alertas.</p>
    @endif

    <h2>Situação por parâmetro</h2>
    <table class="dados">
        <thead>
            <tr>
                <th>Parâmetro</th>
                <th>Situação</th>
                <th class="num">Na faixa</th>
                <th class="num">Faixa ideal</th>
                <th class="num">Mín</th>
                <th class="num">Média</th>
                <th class="num">Máx</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($diagnostico['parametros'] as $p)
                <tr>
                    <td>{{ $p['rotulo'] }}<br><span class="vazio">{{ $p['sensor']->ds_nome }}</span></td>
                    <td class="situacao {{ $p['situacao'] }}">{{ $rotulos[$p['situacao']] }}</td>
                    <td class="num">{{ $p['percentualDentro'] !== null ? $p['percentualDentro'] . '%' : '—' }}</td>
                    <td class="num">
                        @if ($p['faixa'])
                            {{ \App\Utils\Util::formatNumber($p['faixa']['min'], 1) ?? '—' }} a {{ \App\Utils\Util::formatNumber($p['faixa']['max'], 1) ?? '—' }} {{ $p['unidade'] }}
                        @else
                            —
                        @endif
                    </td>
                    @foreach (['minimo', 'media', 'maximo'] as $campo)
                        <td class="num">{{ \App\Utils\Util::formatNumber($p['resumo'][$campo], 1) ? \App\Utils\Util::formatNumber($p['resumo'][$campo], 1) . ' ' . $p['unidade'] : '—' }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td colspan="7" style="color:#4b5563">{{ $p['diagnostico'] }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="vazio">Nenhum sensor nesta seleção.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Alertas mais recentes do período</h2>
    <table class="dados">
        <thead>
            <tr><th>Data</th><th>Mensagem</th><th>Severidade</th></tr>
        </thead>
        <tbody>
            @forelse ($alertas as $alerta)
                <tr>
                    <td class="num" style="text-align:left">{{ $alerta->dt_alerta->format('d/m/Y H:i') }}</td>
                    <td>{{ $alerta->ds_mensagem }}</td>
                    <td>{{ ['critical' => 'Crítico', 'info' => 'Informativo'][$alerta->tp_severidade] ?? 'Atenção' }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="vazio">Nenhum alerta no período.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="rodape">AgroTwin · monitoramento de solo com IoT</p>
</body>
</html>
