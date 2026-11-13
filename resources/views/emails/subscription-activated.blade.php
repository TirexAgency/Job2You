<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Job2You — Abonnement activé</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; color: #1e293b;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color: #f3f4f6; padding: 32px 16px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width: 570px; background-color: #ffffff; border-radius: 16px; overflow: hidden;">
                    <tr>
                        <td style="padding: 28px 36px; background: linear-gradient(135deg, #004ac6 0%, #2563eb 100%); color: #ffffff;">
                            <div style="font-size: 20px; font-weight: 700;">Job2You</div>
                            <div style="font-size: 13px; opacity: .8; margin-top: 4px;">Votre espace carrière</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 36px;">
                            <div style="width: 56px; height: 56px; border-radius: 50%; background: #ecfdf5; color: #059669; font-size: 28px; line-height: 56px; text-align: center; margin-bottom: 20px;">✓</div>

                            <h1 style="margin: 0 0 12px; font-size: 24px; line-height: 1.3;">Votre abonnement est actif</h1>

                            <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #475569;">
                                Bonjour {{ $user->name }}, votre formule <strong>{{ $plan->name }}</strong> a bien été activée.
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 14px 18px; font-size: 14px; color: #64748b;">Formule</td>
                                    <td style="padding: 14px 18px; font-size: 14px; font-weight: 600; text-align: right;">{{ $plan->name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 14px 18px; font-size: 14px; color: #64748b; border-top: 1px solid #e2e8f0;">Quota SMS</td>
                                    <td style="padding: 14px 18px; font-size: 14px; font-weight: 600; text-align: right; border-top: 1px solid #e2e8f0;">{{ $plan->sms_quota }} SMS</td>
                                </tr>
                                <tr>
                                    <td style="padding: 14px 18px; font-size: 14px; color: #64748b; border-top: 1px solid #e2e8f0;">Début</td>
                                    <td style="padding: 14px 18px; font-size: 14px; font-weight: 600; text-align: right; border-top: 1px solid #e2e8f0;">{{ $subscription->starts_at?->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 14px 18px; font-size: 14px; color: #64748b; border-top: 1px solid #e2e8f0;">Fin</td>
                                    <td style="padding: 14px 18px; font-size: 14px; font-weight: 600; text-align: right; border-top: 1px solid #e2e8f0;">{{ $subscription->ends_at?->format('d/m/Y') ?? 'Sans expiration' }}</td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 24px; font-size: 15px; line-height: 1.6; color: #475569;">
                                @if ($plan->cv_parsing_enabled)
                                    L'analyse de CV par IA est incluse dans votre formule.
                                @endif
                                Vous pouvez suivre votre consommation et changer de formule à tout moment depuis votre espace.
                            </p>

                            <a href="{{ route('candidate.subscription') }}" style="display: inline-block; background: #004ac6; color: #ffffff; text-decoration: none; padding: 14px 24px; border-radius: 10px; font-weight: 600;">
                                Voir mon abonnement
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 36px; background: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; text-align: center;">
                            Vous recevez cet email car un abonnement a été activé sur votre compte Job2You.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
