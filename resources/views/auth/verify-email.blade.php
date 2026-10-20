<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="shell-type" content="web_standard">
    <title>Job2You — Vérification email</title>
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
            max-width: 460px;
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

        .icon-circle {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: #eff6ff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            color: #2563eb;
        }

        .icon-circle i { font-size: 2.75rem; }

        .auth-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
            text-align: center;
            margin-bottom: 0.75rem;
        }

        .auth-subtitle {
            text-align: center;
            color: #64748b;
            font-size: 0.875rem;
            margin-bottom: 2rem;
            line-height: 1.6;
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary-full:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(37, 99, 235, 0.4);
        }

        .btn-link {
            background: transparent;
            border: none;
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            padding: 0;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .btn-link:hover { color: #2563eb; text-decoration: underline; }

        .divider {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 1.75rem 0 1.25rem;
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

        .alert-custom.success { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }
        .alert-custom i { flex-shrink: 0; font-size: 1rem; }

        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="auth-page">
        <div class="auth-box">
            <div class="auth-avatar">
                <img src="{{ asset('logo.png') }}" alt="Job2You">
            </div>
            
            <div class="icon-circle">
                <i class="bi bi-envelope-check-fill"></i>
            </div>
            
            <h1 class="auth-title">Vérifiez votre adresse email</h1>
            <p class="auth-subtitle">
                Un lien de vérification a été envoyé à votre adresse email. Cliquez sur ce lien pour activer votre compte.
            </p>

            @if(session('success'))
                <div class="alert-custom success">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('message'))
                <div class="alert-custom success">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('message') }}</span>
                </div>
            @endif

            @if (session('resent'))
                <div class="alert-custom success">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('resent') }}</span>
                </div>
            @endif

            @auth
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn-primary-full">
                        <i class="bi bi-send-fill"></i>
                        <span>Renvoyer le lien</span>
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <div class="field-group">
                        <label for="email" class="field-label">
                            <i class="bi bi-envelope-fill"></i>
                            <span>Adresse email</span>
                        </label>
                        <input type="email" name="email" id="email"
                               class="field-input"
                               placeholder="nom@exemple.com" required autocomplete="email">
                    </div>
                    <button type="submit" class="btn-primary-full">
                        <i class="bi bi-send-fill"></i>
                        <span>Renvoyer le lien</span>
                    </button>
                </form>
            @endauth

            <hr class="divider">

            <div class="text-center">
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn-link">Se déconnecter</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-link">Se connecter</a>
                @endauth
            </div>
        </div>
    </div>
</body>
</html>
