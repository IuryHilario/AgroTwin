<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Helvetica, Arial, sans-serif; color: #1f2937; margin: 0; padding: 24px; background: #f3f4f6;">
    <div style="max-width: 480px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden;">
        <div style="background: #16a34a; color: #fff; padding: 20px;">
            <h1 style="margin: 0; font-size: 18px;">AgroTwin</h1>
            <p style="margin: 4px 0 0; opacity: .9;">Relatório de {{ $insumo->ds_nome }}</p>
        </div>
        <div style="padding: 20px;">
            <p>Olá! Segue em anexo o relatório de consumo e custo de <strong>{{ $insumo->ds_nome }}</strong> dos últimos {{ $relatorio['periodoDias'] }} dias.</p>
            <ul style="padding-left: 18px; color: #374151;">
                <li>Consumo total: <strong>{{ number_format($relatorio['consumoTotal'], 2, ',', '.') }} {{ $insumo->tp_unidade_medida?->value ?? 'UN' }}</strong></li>
                <li>Custo estimado: <strong>R$ {{ number_format($relatorio['custoTotal'], 2, ',', '.') }}</strong></li>
                <li>Área tratada: <strong>{{ number_format($relatorio['areaTratada'], 2, ',', '.') }} ha</strong></li>
            </ul>
            <p style="color: #6b7280; font-size: 13px;">Relatório gerado automaticamente pelo AgroTwin em {{ now()->format('d/m/Y H:i') }}.</p>
        </div>
    </div>
</body>
</html>
