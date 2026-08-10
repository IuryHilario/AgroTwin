<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Novo alerta — AgroTwin</title>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family: Arial, sans-serif;">
    <div style="max-width:480px; margin:0 auto; padding:32px 24px;">
        <h1 style="color:#16a34a; font-size:20px; margin-bottom:16px;">🌱 AgroTwin</h1>

        <div style="background:#ffffff; border-radius:12px; padding:24px; box-shadow:0 2px 8px rgba(0,0,0,0.08);">
            <h2 style="font-size:16px; color:#111827; margin-top:0;">Novo alerta gerado</h2>

            <p style="color:#374151; font-size:14px; line-height:1.5;">{{ $alerta->ds_mensagem }}</p>

            <table style="width:100%; font-size:13px; color:#6b7280; margin-top:16px;">
                <tr>
                    <td style="padding:4px 0;">Sensor</td>
                    <td style="padding:4px 0; text-align:right; color:#111827;">{{ $alerta->sensor->ds_nome }}</td>
                </tr>
                @if($alerta->lavoura)
                    <tr>
                        <td style="padding:4px 0;">Lavoura</td>
                        <td style="padding:4px 0; text-align:right; color:#111827;">{{ $alerta->lavoura->ds_cultura }}</td>
                    </tr>
                @endif
                <tr>
                    <td style="padding:4px 0;">Data</td>
                    <td style="padding:4px 0; text-align:right; color:#111827;">{{ $alerta->dt_alerta->format('d/m/Y H:i') }}</td>
                </tr>
            </table>

            <a href="{{ route('alertas.index') }}" style="display:inline-block; margin-top:20px; background:#16a34a; color:#ffffff; text-decoration:none; padding:10px 18px; border-radius:8px; font-size:14px;">
                Ver no AgroTwin
            </a>
        </div>

        <p style="color:#9ca3af; font-size:12px; margin-top:20px;">
            Você recebeu este e-mail porque a notificação de alertas está ativada nas suas
            <a href="{{ route('configuracoes.edit') }}" style="color:#16a34a;">Configurações</a> do AgroTwin.
        </p>
    </div>
</body>
</html>
