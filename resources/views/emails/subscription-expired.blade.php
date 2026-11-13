<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Job2You — Abonnement expiré</title>
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
                            <div style="width: 56px; height: 56px; border-radius: 50%; background: #fff7ed; color: #ea580c; font-size: 28px; line-height: 56px; text-align: center; margin-bottom: 20px;">!</div>

                            <h1 style="margin: 0 0 12px; font-size: 24px; line-height: 1.3;">Votre abonnement a expiré</h1>

                            <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #475569;">
                                Bonjour {{ $user->name }}, votre formule <strong>{{ $plan->name }}</strong> est arrivée à échéance le {{ $subscription->ends_at?->format('d/m/Y') ?? 'aujourd’hui' }}.
                            </p>

                            @if ($fallbackPlan)
                                <p style="margin: 0 0 24px; font-size: 15px; line-height: 1.6; color: #475569;">
                                    Votre compte a été automatiquement rétrogradé vers la formule <strong>{{ $fallbackPlan->name }}</strong> avec un quota de {{ $fallbackPlan->sms_quota }} SMS.
                                    Vous conservez l'accès aux offres et à vos recommandations.
                                </p>
                            @else
                                <p style="margin: 0 0 24px; font-size: 15px; line-height: 1.6; color: #475569;">
                                    Votre compte reste accessible avec les fonctionnalités de base. Choisissez une nouvelle formule pour retrouver vos quotas et vos avantages.
                                </p>
                            @endif

                            <a href="{{ route('candidate.subscription') }}" style="display: inline-block; background: #004ac6; color: #ffffff; text-decoration: none; padding: 14px 24px; border-radius: 10px; font-weight: 600;">
                                Choisir une formule
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 36px; background: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; text-align: center;">
                            Vous recevez cet email car votre abonnement Job2You est arrivé à expiration.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
