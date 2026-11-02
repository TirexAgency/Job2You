<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>Job2You — Réinitialisation mot de passe</title>
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body { width: 100% !important; }
            .footer { width: 100% !important; }
            .logo-img { width: 120px !important; height: 30px !important; }
            .btn { width: 100% !important; max-width: 100% !important; }
        }
        @media only screen and (max-width: 480px) {
            .content-cell { padding: 20px 15px !important; }
            .btn { padding: 14px 20px !important; font-size: 15px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;">
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color: #f3f4f6;">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    <!-- Header with Logo -->
                    <tr>
                        <td class="header" align="center" style="padding: 40px 20px 20px; background-color: #ffffff;">
                            <a href="https://job2you.live-preprod.site" style="text-decoration: none; display: inline-block;">
                                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABOYAAATmCAIAAAAKnjl9AABcZmNhQlgAAFxmanVtYgAAAB5qdW1kYzJwYQARABCAAACqADibcQNjMnBhAAAAXEBqdW1iAAAAR2p1bWRjMm1hABEAEIAAAKoAOJtxA3VybjpjMnBhOjA3NjM2ZTIwLTVkMGQtNGQ4Ni1hYTNlLWMwNjUy" alt="Job2You" class="logo-img" style="width: 140px; height: 35px; object-fit: cover; border: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic;">
                            </a>
                        </td>
                    </tr>

                    <!-- Email Body -->
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0" style="border: hidden !important; background-color: #ffffff;">
                            <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="max-width: 570px; margin: 0 auto;">
                                <tr>
                                    <td class="content-cell" style="padding: 0 40px 40px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">

                                        <!-- Icon -->
                                        <div style="text-align: center; margin-bottom: 24px;">
                                            <div style="width: 72px; height: 72px; border-radius: 50%; background: #fef2f2; display: inline-flex; align-items: center; justify-content: center; margin: 0 auto;">
                                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: block;">
                                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                                </svg>
                                            </div>
                                        </div>

                                        <!-- Title -->
                                        <h1 style="margin: 0 0 16px; font-size: 24px; font-weight: 700; color: #1e293b; text-align: center; line-height: 1.3;">
                                            Réinitialisez votre mot de passe
                                        </h1>

                                        <!-- Intro text -->
                                        <p style="margin: 0 0 24px; font-size: 16px; color: #475569; text-align: center; line-height: 1.6;">
                                            Vous avez demandé la réinitialisation de votre mot de passe sur <strong>Job2You</strong>.<br>
                                            Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe.
                                        </p>

                                        <!-- CTA Button -->
                                        <div style="text-align: center; margin: 32px 0;">
                                            <a href="{{ $resetUrl }}" class="btn" style="display: inline-block; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #ffffff; text-decoration: none; padding: 16px 32px; border-radius: 50px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3); transition: all 0.2s ease;">
                                                <span style="display: inline-flex; align-items: center; gap: 8px;">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display: block;">
                                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                                    </svg>
                                                    Cliquez ici pour réinitialiser
                                                </span>
                                            </a>
                                        </div>

                                        <!-- Alternative link -->
                                        <p style="margin: 24px 0 0; font-size: 13px; color: #94a3b8; text-align: center; line-height: 1.5;">
                                            Si le bouton ne fonctionne pas, <a href="{{ $resetUrl }}" style="color: #2563eb; text-decoration: underline;">cliquez ici</a>.
                                        </p>

                                        <!-- Security note -->
                                        <div style="margin-top: 32px; padding: 16px; background: #fef3c7; border: 1px solid #fde68a; border-radius: 8px;">
                                            <p style="margin: 0; font-size: 13px; color: #92400e; line-height: 1.5;">
                                                <strong>⚠ Sécurité :</strong> Ce lien expire dans 60 minutes. Si vous n'avez pas demandé cette réinitialisation, ignorez cet email et votre mot de passe restera inchangé.
                                            </p>
                                        </div>

                                        <!-- Signature -->
                                        <hr style="margin: 32px 0; border: none; border-top: 1px solid #e2e8f0;">
                                        <p style="margin: 0; font-size: 14px; color: #64748b; text-align: center; line-height: 1.6;">
                                            Cordialement,<br>
                                            <strong>Job2You</strong>
                                        </p>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="footer" align="center" style="padding: 24px 20px; background-color: #ffffff; border-top: 1px solid #e2e8f0;">
                            <table class="footer-content" width="570" cellpadding="0" cellspacing="0" role="presentation" style="max-width: 570px; margin: 0 auto;">
                                <tr>
                                    <td style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; font-size: 12px; color: #94a3b8; text-align: center; line-height: 1.6;">
                                        <p style="margin: 0 0 8px;">
                                            © 2025 Job2You. Tous droits réservés.
                                        </p>
                                        <p style="margin: 0;">
                                            Plateforme de matching d'offres d'emploi avec alertes SMS automatisées
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>