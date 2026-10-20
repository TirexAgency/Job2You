<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="shell-type" content="web_standard">
    <title>Job2You — Connexion</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; height: 100%; overflow-x: hidden; }
        body { font-family: 'Inter', sans-serif; }

        .auth-fullscreen {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* ===== SECTION GAUCHE : ILLUSTRATION ===== */
        .auth-visual {
            flex: 1;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 50%, #1e40af 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 3rem;
        }

        .auth-visual::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -10%;
            width: 70%;
            height: 70%;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 45% 55% 60% 40% / 50% 45% 55% 50%;
            animation: blobFloat 12s ease-in-out infinite;
        }

        .auth-visual::after {
            content: '';
            position: absolute;
            bottom: -15%;
            left: -10%;
            width: 60%;
            height: 60%;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 60% 40% 45% 55% / 55% 60% 40% 45%;
            animation: blobFloat 15s ease-in-out infinite reverse;
        }

        @keyframes blobFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(20px, -30px) scale(1.08); }
        }

        .shape-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            animation: floatShape 18s ease-in-out infinite;
        }

        .shape-circle.c1 { width: 120px; height: 120px; top: 10%; left: 8%; }
        .shape-circle.c2 { width: 80px; height: 80px; top: 60%; right: 12%; animation-delay: 3s; }
        .shape-circle.c3 { width: 200px; height: 200px; bottom: -40px; right: -40px; animation-delay: 6s; }
        .shape-circle.c4 { width: 60px; height: 60px; top: 25%; right: 20%; animation-delay: 9s; }

        @keyframes floatShape {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(15px, -20px) scale(1.1); }
            50% { transform: translate(-15px, 15px) scale(0.95); }
            75% { transform: translate(10px, 20px) scale(1.05); }
        }

        .visual-content {
            position: relative;
            z-index: 2;
            max-width: 520px;
            width: 100%;
            text-align: center;
            color: white;
        }

        .visual-illustration {
            width: 100%;
            max-width: 380px;
            margin: 0 auto 2.5rem;
            display: block;
            filter: drop-shadow(0 20px 40px rgba(0, 0, 0, 0.15));
            animation: floatIllustration 6s ease-in-out infinite;
        }

        @keyframes floatIllustration {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }

        .visual-title {
            font-size: 2.25rem;
            font-weight: 800;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
            color: #ffffff;
        }

        .visual-subtitle {
            font-size: 1.05rem;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .visual-features {
            display: flex;
            flex-direction: column;
            gap: 0.875rem;
            text-align: left;
            max-width: 320px;
            margin: 0 auto;
        }

        .visual-feature {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 0.875rem;
            transition: all 0.3s ease;
        }

        .visual-feature:hover {
            background: rgba(255, 255, 255, 0.18);
            transform: translateX(4px);
        }

        .visual-feature i { font-size: 1.25rem; flex-shrink: 0; }
        .visual-feature span { font-size: 0.9rem; color: rgba(255, 255, 255, 0.95); font-weight: 500; }

        /* ===== SECTION DROITE : FORMULAIRE ===== */
        .auth-form-side {
            flex: 1;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            position: relative;
        }

        .form-container { width: 100%; max-width: 420px; }

        .form-avatar {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            border: 4px solid #ffffff;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.15);
            overflow: hidden;
        }

        .form-avatar img { width: 100%; height: 100%; object-fit: cover; object-position: center 46%; }

        .form-title {
            font-size: 2rem;
            font-weight: 800;
            color: #1e293b;
            text-align: center;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .form-subtitle {
            text-align: center;
            color: #64748b;
            font-size: 0.95rem;
            margin-bottom: 2.5rem;
        }

        .field-group { margin-bottom: 1.75rem; position: relative; }

        .field-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #2563eb;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .field-label i { font-size: 1rem; }

        .field-input {
            width: 100%;
            border: none;
            border-bottom: 2px solid #e2e8f0;
            padding: 0.75rem 0;
            font-size: 1rem;
            color: #1e293b;
            background: transparent;
            outline: none;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .field-input::placeholder { color: #94a3b8; font-weight: 400; }
        .field-input:focus { border-bottom-color: #2563eb; }
        .field-input:focus::placeholder { color: #cbd5e1; }
        .field-input.is-invalid { border-bottom-color: #ef4444; }

        .field-error { color: #ef4444; font-size: 0.8rem; margin-top: 0.375rem; }

        .forgot-link {
            display: block;
            text-align: right;
            color: #94a3b8;
            font-size: 0.85rem;
            text-decoration: none;
            margin-top: -0.5rem;
            margin-bottom: 2rem;
            transition: color 0.2s ease;
        }

        .forgot-link:hover { color: #2563eb; }

        .btn-primary-full {
            width: 100%;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            border: none;
            padding: 1rem 1.5rem;
            font-size: 1rem;
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
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        }

        .btn-primary-full:active { transform: translateY(0); }

        .password-wrapper { position: relative; }

        .password-toggle {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            font-size: 1.1rem;
            transition: color 0.2s ease;
            background: transparent;
            border: none;
            padding: 0.25rem;
        }

        .password-toggle:hover { color: #2563eb; }

        .switch-auth {
            text-align: center;
            margin-top: 2rem;
            color: #64748b;
            font-size: 0.9rem;
        }

        .switch-auth a { color: #2563eb; font-weight: 600; text-decoration: none; transition: color 0.2s ease; }
        .switch-auth a:hover { color: #1d4ed8; text-decoration: underline; }

        .checkbox-custom {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: #64748b;
            cursor: pointer;
            margin-bottom: 1.5rem;
        }

        .checkbox-custom input { accent-color: #2563eb; width: 16px; height: 16px; cursor: pointer; }

        .alert-custom {
            padding: 0.875rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .alert-custom.success { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }
        .alert-custom.danger { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alert-custom i { flex-shrink: 0; font-size: 1.1rem; }

        @media (max-width: 991.98px) {
            .auth-visual { display: none; }
            .auth-form-side { flex: 1 1 100%; padding: 2rem 1.5rem; min-height: 100vh; }
            .form-container { max-width: 440px; }
        }

        @media (max-width: 575.98px) {
            .form-title { font-size: 1.5rem; }
            .form-avatar { width: 80px; height: 80px; }
            .auth-form-side { padding: 2rem 1.25rem; }
        }
    </style>
</head>
<body>
    <div class="auth-fullscreen">
        
        <!-- ===== SECTION GAUCHE : ILLUSTRATION ===== -->
        <div class="auth-visual">
            <div class="shape-circle c1"></div>
            <div class="shape-circle c2"></div>
            <div class="shape-circle c3"></div>
            <div class="shape-circle c4"></div>
            
            <div class="visual-content">
                <svg class="visual-illustration" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M50 200 Q 100 100 200 120 T 350 200 Q 320 320 200 340 T 50 200 Z" fill="rgba(255,255,255,0.08)"/>
                    <path d="M80 250 Q 120 180 200 190 T 320 260 Q 280 340 200 350 T 80 250 Z" fill="rgba(255,255,255,0.06)"/>
                    
                    <rect x="140" y="80" width="120" height="220" rx="18" fill="#1e40af" stroke="#ffffff" stroke-width="3"/>
                    <rect x="148" y="88" width="104" height="204" rx="12" fill="#2563eb"/>
                    <rect x="180" y="94" width="40" height="4" rx="2" fill="#ffffff" opacity="0.6"/>
                    <rect x="155" y="105" width="90" height="170" rx="6" fill="rgba(255,255,255,0.15)"/>
                    
                    <circle cx="200" cy="170" r="16" fill="#fbbf9e"/>
                    <path d="M184 168 Q 200 152 216 168 Q 216 158 200 156 Q 184 158 184 168 Z" fill="#3d2c1e"/>
                    <rect x="184" y="188" width="32" height="40" rx="4" fill="#ffffff"/>
                    <path d="M198 188 L 200 200 L 202 188 Z" fill="#1d4ed8"/>
                    <rect x="176" y="192" width="8" height="30" rx="3" fill="#e5e7eb"/>
                    <rect x="216" y="192" width="8" height="30" rx="3" fill="#e5e7eb"/>
                    <rect x="186" y="228" width="10" height="28" rx="3" fill="#3d4a5c"/>
                    <rect x="204" y="228" width="10" height="28" rx="3" fill="#3d4a5c"/>
                    <ellipse cx="191" cy="258" rx="7" ry="4" fill="#1f2937"/>
                    <ellipse cx="209" cy="258" rx="7" ry="4" fill="#1f2937"/>
                    <path d="M148 88 L 180 88 L 148 180 Z" fill="rgba(255,255,255,0.08)"/>
                </svg>
                
                <h2 class="visual-title">Bienvenue sur Job2You</h2>
                <p class="visual-subtitle">
                    La plateforme qui connecte talents et opportunités à Madagascar
                </p>
                
                <div class="visual-features">
                    <div class="visual-feature">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Matching intelligent avec l'IA</span>
                    </div>
                    <div class="visual-feature">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Profils vérifiés et sécurisés</span>
                    </div>
                    <div class="visual-feature">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Alertes SMS en temps réel</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== SECTION DROITE : FORMULAIRE ===== -->
        <div class="auth-form-side">
            <div class="form-container">
                
                <div class="form-avatar">
                    <img src="{{ asset('logo.png') }}" alt="Job2You">
                </div>
                
                <h1 class="form-title">CONNEXION</h1>
                <p class="form-subtitle">Accédez à votre espace Job2You</p>

                @if (session('success'))
                    <div class="alert-custom success">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('status'))
                    <div class="alert-custom success">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert-custom danger">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="field-group">
                        <label for="identifier" class="field-label">
                            <i class="bi bi-person-fill"></i>
                            <span>Email ou téléphone</span>
                        </label>
                        <input type="text"
                               class="field-input @error('email') is-invalid @enderror"
                               id="identifier" name="email" value="{{ old('email') }}"
                               placeholder="nom@exemple.com ou +261 34 00 000 00"
                               required autofocus autocomplete="username">
                        @error('email')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="password" class="field-label">
                            <i class="bi bi-lock-fill"></i>
                            <span>Mot de passe</span>
                        </label>
                        <div class="password-wrapper">
                            <input type="password"
                                   class="field-input @error('password') is-invalid @enderror"
                                   id="password" name="password"
                                   placeholder="••••••••"
                                   required autocomplete="current-password">
                            <button type="button" class="password-toggle" id="togglePassword" aria-label="Afficher le mot de passe">
                                <i class="bi bi-eye-slash" id="passwordIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <a href="{{ route('password.request') }}" class="forgot-link">
                        Mot de passe oublié ?
                    </a>

                    <label class="checkbox-custom">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                        <span>Se souvenir de moi</span>
                    </label>

                    <button type="submit" class="btn-primary-full">
                        Se connecter
                    </button>
                </form>

                <div class="switch-auth">
                    Pas encore de compte ?
                    <a href="{{ route('register') }}">Créer un compte</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const toggleBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');

            if (toggleBtn && passwordInput && passwordIcon) {
                toggleBtn.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    passwordIcon.classList.toggle('bi-eye', isPassword);
                    passwordIcon.classList.toggle('bi-eye-slash', !isPassword);
                });
            }
        })();
    </script>
</body>
</html>
