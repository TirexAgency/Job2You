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
</head>
<body class="bg-light job2you-public-body">
    <main class="job2you-public-main d-flex flex-column justify-content-center align-items-center px-3 px-md-4">
        <div class="position-relative w-100 job2you-auth-wrapper">

            <!-- Retour à l'accueil -->
            <a href="{{ route('home') }}" class="job2you-back-link d-inline-flex align-items-center gap-1 small text-secondary text-decoration-none mb-3">
                <i class="bi bi-arrow-left"></i>
                <span>Retour à l'accueil</span>
            </a>

            <!-- Carte principale -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">

                    <!-- En-tête de carte -->
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center p-2 bg-light rounded-3 mb-3">
                            <img src="{{ asset('logo.png') }}" alt="Job2You Logo" class="rounded-3" style="height: 2.5rem; width: auto; object-fit: cover; object-position: center 46%;">
                        </div>
                        <h1 class="h3 fw-bold text-dark mb-2">Connexion</h1>
                        <p class="text-secondary mb-0">Accédez à votre espace candidat ou recruteur</p>
                    </div>

                    <!-- Messages de session -->
                    @if (session('success'))
                        <div class="alert alert-success d-flex align-items-start gap-2" role="status">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="alert alert-info d-flex align-items-start gap-2" role="status">
                            <i class="bi bi-info-circle-fill"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger d-flex align-items-start gap-2" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div>
                                @foreach ($errors->all() as $error)
                                    <p class="mb-0">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Formulaire d'authentification -->
                    <form method="POST" action="{{ route('login') }}" class="d-flex flex-column gap-3">
                        @csrf

                        <!-- Identifiant -->
                        <div>
                            <label for="identifier" class="form-label d-flex justify-content-between align-items-center mb-1">
                                <span>Email ou téléphone</span>
                                <span class="small text-secondary fw-normal">Requis</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-secondary">
                                    <i class="bi bi-at"></i>
                                </span>
                                <input type="text"
                                       class="form-control bg-light border-start-0 @error('email') is-invalid @enderror"
                                       id="identifier" name="email" value="{{ old('email') }}"
                                       placeholder="nom@exemple.com ou +261 34 00 000 00"
                                       required autofocus autocomplete="username">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Mot de passe -->
                        <div>
                            <label for="password" class="form-label mb-1">Mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-secondary">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password"
                                       class="form-control bg-light border-start-0 border-end-0 @error('password') is-invalid @enderror"
                                       id="password" name="password" placeholder="••••••••"
                                       required autocomplete="current-password">
                                <button class="btn btn-outline-secondary border-start-0 bg-light text-secondary" type="button" id="togglePassword" aria-label="Afficher ou masquer le mot de passe">
                                    <i class="bi bi-eye-slash" id="passwordIcon"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Options (se souvenir / mot de passe oublié) -->
                        <div class="d-flex justify-content-between align-items-center pt-1">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1" @checked(old('remember'))>
                                <label class="form-check-label small text-secondary" for="remember">Se souvenir de moi</label>
                            </div>
                            <a href="{{ route('password.request') }}" class="small fw-medium text-decoration-none">Mot de passe oublié ?</a>
                        </div>

                        <!-- Bouton de soumission -->
                        <button type="submit" class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center gap-2 mt-2">
                            <span>Se connecter</span>
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </form>

                    <!-- Séparateur -->
                    <div class="position-relative text-center my-4">
                        <hr class="my-0">
                        <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 small text-secondary text-uppercase">ou</span>
                    </div>

                    <!-- Pied de carte -->
                    <div class="text-center small">
                        <span class="text-secondary">Vous n'avez pas encore de compte ?</span>
                        <a href="{{ route('register') }}" class="fw-semibold text-decoration-none ms-1">Créer un compte</a>
                    </div>
                </div>
            </div>

            <!-- Indicateurs de confiance -->
            <div class="d-flex flex-wrap justify-content-center align-items-center gap-3 gap-md-4 mt-3 text-secondary small">
                <span class="d-inline-flex align-items-center gap-1">
                    <i class="bi bi-lock-fill text-success"></i>
                    Chiffrement AES 256-bit
                </span>
                <span class="text-muted">•</span>
                <span class="d-inline-flex align-items-center gap-1">
                    <i class="bi bi-shield-check text-primary"></i>
                    Conforme RGPD
                </span>
            </div>
        </div>
    </main>

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
