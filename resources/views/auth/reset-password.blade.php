<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="shell-type" content="web_standard">
    <title>Job2You — Réinitialiser le mot de passe</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; min-height: 100%; }
        body { font-family: 'Inter', sans-serif; }

        .auth-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 50%, #1e40af 100%);
            position: relative;
            overflow: hidden;
        }

        .auth-page::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -10%;
            width: 60%;
            height: 60%;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 45% 55% 60% 40% / 50% 45% 55% 50%;
            animation: blobFloat 12s ease-in-out infinite;
        }

        .auth-page::after {
            content: '';
            position: absolute;
            bottom: -15%;
            left: -10%;
            width: 50%;
            height: 50%;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 60% 40% 45% 55% / 55% 60% 40% 45%;
            animation: blobFloat 15s ease-in-out infinite reverse;
        }

        @keyframes blobFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(20px, -30px) scale(1.08); }
        }

        .auth-box {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 1.5rem;
            padding: 2.5rem 2rem;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.2);
        }

        .auth-avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #eff6ff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            overflow: hidden;
        }

        .auth-avatar img { width: 100%; height: 100%; object-fit: cover; object-position: center 46%; }

        .auth-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .auth-subtitle {
            text-align: center;
            color: #64748b;
            font-size: 0.875rem;
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        .field-group { margin-bottom: 1.5rem; }

        .field-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: #2563eb;
            margin-bottom: 0.375rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .field-input {
            width: 100%;
            border: none;
            border-bottom: 2px solid #e2e8f0;
            padding: 0.625rem 0;
            font-size: 0.95rem;
            color: #1e293b;
            background: transparent;
            outline: none;
            transition: border-color 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .field-input::placeholder { color: #94a3b8; }
        .field-input:focus { border-bottom-color: #2563eb; }
        .field-input.is-invalid { border-bottom-color: #ef4444; }

        .field-error { color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; }

        .password-wrapper { position: relative; }

        .password-toggle {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            font-size: 1rem;
            transition: color 0.2s ease;
            background: transparent;
            border: none;
            padding: 0.25rem;
        }

        .password-toggle:hover { color: #2563eb; }

        .btn-primary-full {
            width: 100%;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            border: none;
            padding: 1rem 1.5rem;
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }

        .btn-primary-full:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(37, 99, 235, 0.4);
        }

        .alert-custom {
            padding: 0.875rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .alert-custom.danger { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alert-custom i { flex-shrink: 0; font-size: 1rem; }
    </style>
</head>
<body>
    <div class="auth-page">
        <div class="auth-box">
            <div class="auth-avatar">
                <img src="{{ asset('logo.png') }}" alt="Job2You">
            </div>
            
            <h1 class="auth-title">Nouveau mot de passe</h1>
            <p class="auth-subtitle">Créez un nouveau mot de passe sécurisé</p>

            @if($errors->any())
                <div class="alert-custom danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="field-group">
                    <label for="email" class="field-label">
                        <i class="bi bi-envelope-fill"></i>
                        <span>Adresse email</span>
                    </label>
                    <input type="email"
                           class="field-input @error('email') is-invalid @enderror"
                           id="email" name="email" value="{{ old('email', $request->email) }}"
                           required autofocus autocomplete="email">
                    @error('email')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field-group">
                    <label for="password" class="field-label">
                        <i class="bi bi-lock-fill"></i>
                        <span>Nouveau mot de passe</span>
                    </label>
                    <div class="password-wrapper">
                        <input type="password"
                               class="field-input @error('password') is-invalid @enderror"
                               id="password" name="password"
                               placeholder="••••••••" required autocomplete="new-password"
                               oninput="updateResetPasswordStrength(this.value)">
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Afficher le mot de passe">
                            <i class="bi bi-eye-slash" id="passwordIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="field-error">{{ $message }}</div>
                    @enderror

                    <!-- Exigences mot de passe -->
                    <div class="password-requirements" id="pwd-requirements" style="margin-top: 8px; padding: 10px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 12px; color: #64748b; display: none;">
                        <div style="font-weight: 600; color: #334155; margin-bottom: 6px;">Exigences :</div>
                        <ul style="margin: 0; padding-left: 18px; line-height: 1.8;">
                            <li id="req-length" class="text-muted">Minimum 8 caractères</li>
                            <li id="req-uppercase" class="text-muted">Au moins 1 majuscule</li>
                            <li id="req-number" class="text-muted">Au moins 1 chiffre</li>
                            <li id="req-special" class="text-muted">Au moins 1 caractère spécial</li>
                        </ul>
                    </div>
                </div>

                <div class="field-group">
                    <label for="password_confirmation" class="field-label">
                        <i class="bi bi-lock-fill"></i>
                        <span>Confirmer le mot de passe</span>
                    </label>
                    <div class="password-wrapper">
                        <input type="password"
                               class="field-input"
                               id="password_confirmation" name="password_confirmation"
                               placeholder="••••••••" required autocomplete="new-password">
                        <button type="button" class="password-toggle" id="togglePasswordConfirmation" aria-label="Afficher la confirmation">
                            <i class="bi bi-eye-slash" id="passwordConfirmationIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary-full">
                    Réinitialiser
                </button>
            </form>
        </div>
    </div>

    <script>
        function setupToggle(toggleId, inputId, iconId) {
            const toggleBtn = document.getElementById(toggleId);
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (!toggleBtn || !input || !icon) return;
            toggleBtn.addEventListener('click', function () {
                const isPassword = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPassword ? 'text' : 'password');
                icon.classList.toggle('bi-eye', isPassword);
                icon.classList.toggle('bi-eye-slash', !isPassword);
            });
        }

        setupToggle('togglePassword', 'password', 'passwordIcon');
        setupToggle('togglePasswordConfirmation', 'password_confirmation', 'passwordConfirmationIcon');

        // Password strength indicator
        function updateResetPasswordStrength(val) {
            const bar = document.getElementById('strength-bar');
            const label = document.getElementById('strength-label');
            const requirements = document.getElementById('pwd-requirements');
            if (!bar || !label) return;

            let score = 0;
            const checks = {
                length: val.length >= 8,
                uppercase: /[A-Z]/.test(val),
                number: /[0-9]/.test(val),
                special: /[^A-Za-z0-9]/.test(val),
            };

            if (checks.length) score += 25;
            if (checks.uppercase) score += 25;
            if (checks.number) score += 25;
            if (checks.special) score += 25;

            bar.style.width = score + '%';

            // Mettre à jour les exigences visuellement
            if (requirements) {
                requirements.style.display = val.length > 0 ? 'block' : 'none';
                updateRequirement('req-length', checks.length);
                updateRequirement('req-uppercase', checks.uppercase);
                updateRequirement('req-number', checks.number);
                updateRequirement('req-special', checks.special);
            }

            if (score === 0) {
                bar.style.background = '#e2e8f0';
                label.textContent = 'Minimum 8 caractères';
                label.style.color = '#94a3b8';
            } else if (score <= 50) {
                bar.style.background = '#ef4444';
                label.textContent = 'Faible';
                label.style.color = '#ef4444';
            } else if (score === 75) {
                bar.style.background = '#f59e0b';
                label.textContent = 'Moyen';
                label.style.color = '#f59e0b';
            } else {
                bar.style.background = '#10b981';
                label.textContent = 'Robuste';
                label.style.color = '#10b981';
            }
        }

        function updateRequirement(id, met) {
            const el = document.getElementById(id);
            if (!el) return;
            if (met) {
                el.classList.remove('text-muted');
                el.classList.add('text-success');
                el.style.color = '#10b981';
            } else {
                el.classList.remove('text-success');
                el.classList.add('text-muted');
                el.style.color = '#94a3b8';
            }
        }

        // Add strength bar to reset password page
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            if (passwordInput) {
                // Add strength bar and label after the password wrapper
                const wrapper = passwordInput.closest('.password-wrapper');
                if (wrapper && !document.getElementById('strength-bar')) {
                    const strengthHtml = `
                        <div class="strength-bar" style="height: 3px; background: #e2e8f0; border-radius: 2px; overflow: hidden; margin-top: 0.5rem;">
                            <div class="strength-fill" id="strength-bar" style="height: 100%; border-radius: 2px; transition: width 0.3s ease, background-color 0.3s ease; width: 0%;"></div>
                        </div>
                        <div class="strength-label" id="strength-label" style="font-size: 12px; color: #94a3b8; margin-top: 0.25rem;">Minimum 8 caractères</div>
                    `;
                    wrapper.insertAdjacentHTML('afterend', strengthHtml);
                }
            }
        });
    </script>
</body>
</html>
