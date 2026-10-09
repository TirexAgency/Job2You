<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="shell-type" content="web_standard">
    <title>Job2You — Inscription</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light job2you-public-body">
    @include('partials.header-public')

    <main class="job2you-public-main d-flex flex-column justify-content-center align-items-center px-3 px-md-4">
        <div class="w-100 job2you-auth-wrapper">

            <div class="card border-0 shadow rounded-4">
                <div class="card-body p-4 p-md-5">

                    <!-- En-tête -->
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center p-2 bg-light rounded-3 mb-3 shadow-sm">
                            <img src="{{ asset('logo.png') }}" alt="Job2You Logo" class="rounded-3" style="height: 2.5rem; width: auto; object-fit: cover; object-position: center 46%;">
                        </div>
                        <span class="badge rounded-pill text-bg-light text-primary text-uppercase mb-2">
                            <span class="d-inline-block bg-primary rounded-circle me-1" style="width: 0.375rem; height: 0.375rem;"></span>
                            Espace Candidat &amp; Entreprise
                        </span>
                        <h1 class="h3 fw-bold text-dark mb-2">Rejoignez Job2You</h1>
                        <p class="text-secondary mb-0">Créez votre profil en 2 minutes et activez vos alertes intelligentes</p>
                    </div>

                    <!-- Messages de session -->
                    @if (session('success'))
                        <div class="alert alert-success d-flex align-items-start gap-2" role="status">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- Erreurs de validation -->
                    @if ($errors->any())
                        <div class="alert alert-danger d-flex align-items-start gap-2" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" id="register-form" class="d-flex flex-column gap-3" novalidate>
                        @csrf

                        <!-- Nom / Téléphone -->
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label for="name" class="form-label d-flex justify-content-between align-items-center mb-1">
                                    Nom complet <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control bg-light @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}"
                                       placeholder="ex: Hery Randria" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="phone" class="form-label d-flex justify-content-between align-items-center mb-1">
                                    Téléphone <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-secondary">
                                        <i class="bi bi-telephone"></i>
                                    </span>
                                    <input type="tel"
                                           class="form-control bg-light border-start-0 @error('phone') is-invalid @enderror"
                                           id="phone" name="phone" value="{{ old('phone') }}"
                                           placeholder="+261 34 00 000 00" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <p class="d-flex align-items-center gap-1 small text-secondary mb-0">
                            <i class="bi bi-info-circle text-success"></i>
                            Format Madagascar (ex : +261 34 12 345 67 ou 034 12 345 67) — pour la réception instantanée de vos alertes SMS.
                        </p>

                        <!-- Email -->
                        <div>
                            <label for="email" class="form-label d-flex justify-content-between align-items-center mb-1">
                                Email <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-secondary">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email"
                                       class="form-control bg-light border-start-0 @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}"
                                       placeholder="nom@exemple.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Mot de passe / Confirmation -->
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label for="password" class="form-label d-flex justify-content-between align-items-center mb-1">
                                    Mot de passe <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control bg-light border-end-0 @error('password') is-invalid @enderror"
                                           id="password" name="password" minlength="8"
                                           placeholder="••••••••" required autocomplete="new-password"
                                           oninput="updatePasswordStrength(this.value)">
                                    <button class="btn btn-outline-secondary border-start-0 bg-light text-secondary" type="button"
                                            onclick="toggleVisibility('password', 'pwd-toggle-icon')"
                                            aria-label="Afficher ou masquer le mot de passe">
                                        <i class="bi bi-eye-slash" id="pwd-toggle-icon"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="password_confirmation" class="form-label d-flex justify-content-between align-items-center mb-1">
                                    Confirmation <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control bg-light border-end-0"
                                           id="password_confirmation" name="password_confirmation"
                                           placeholder="••••••••" required autocomplete="new-password">
                                    <button class="btn btn-outline-secondary border-start-0 bg-light text-secondary" type="button"
                                            onclick="toggleVisibility('password_confirmation', 'conf-pwd-toggle-icon')"
                                            aria-label="Afficher ou masquer la confirmation">
                                        <i class="bi bi-eye-slash" id="conf-pwd-toggle-icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Force du mot de passe -->
                        <div>
                            <div class="d-flex justify-content-between align-items-center small text-secondary">
                                <span>Force du mot de passe</span>
                                <span id="strength-label">Minimum 8 caractères</span>
                            </div>
                            <div class="progress mt-1" style="height: 0.375rem;">
                                <div class="progress-bar bg-secondary" id="strength-bar" role="progressbar" style="width: 0%;"></div>
                            </div>
                            <p class="small text-secondary mb-0 mt-1">Majuscule, minuscule, chiffre et symbole requis.</p>
                        </div>

                        <!-- Conditions -->
                        <div class="p-3 rounded-3 bg-light d-flex align-items-start gap-3">
                            <input class="form-check-input mt-1 flex-shrink-0" type="checkbox" id="terms" name="terms" required>
                            <label class="form-check-label small text-secondary" for="terms">
                                J'accepte les
                                <a href="{{ route('terms') }}" class="text-decoration-none fw-medium">conditions d'utilisation</a>
                                et la
                                <a href="{{ route('privacy') }}" class="text-decoration-none fw-medium">politique de confidentialité</a>
                                de Job2You.
                            </label>
                        </div>

                        <!-- Bouton -->
                        <button type="submit" id="submit-btn" class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center gap-2">
                            <span>Créer mon compte</span>
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </form>
                </div>

                <!-- Pied de carte -->
                <div class="card-footer bg-light border-0 rounded-bottom-4 text-center py-3">
                    <p class="small text-secondary mb-0">
                        Vous possédez déjà un compte ?
                        <a href="{{ route('login') }}" class="fw-semibold text-decoration-none ms-1">Se connecter</a>
                    </p>
                </div>
            </div>

            <!-- Indicateurs de confiance -->
            <div class="d-flex flex-wrap justify-content-center align-items-center gap-3 gap-md-4 mt-3 text-secondary small">
                <span class="d-inline-flex align-items-center gap-1">
                    <i class="bi bi-lock-fill text-success"></i>
                    Chiffrement SSL 256-bit
                </span>
                <span class="d-inline-flex align-items-center gap-1">
                    <i class="bi bi-shield-check text-primary"></i>
                    Conforme RGPD
                </span>
            </div>
        </div>
    </main>

    @include('partials.footer-public')

    <script>
        function toggleVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (!input || !icon) return;
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            icon.classList.toggle('bi-eye', isPassword);
            icon.classList.toggle('bi-eye-slash', !isPassword);
        }

        function updatePasswordStrength(val) {
            const bar = document.getElementById('strength-bar');
            const label = document.getElementById('strength-label');
            if (!bar || !label) return;

            let score = 0;
            if (val.length >= 8) score += 25;
            if (/[A-Z]/.test(val)) score += 25;
            if (/[0-9]/.test(val)) score += 25;
            if (/[^A-Za-z0-9]/.test(val)) score += 25;

            bar.style.width = score + '%';

            if (score === 0) {
                bar.className = 'progress-bar bg-secondary';
                label.textContent = 'Minimum 8 caractères';
                label.className = '';
            } else if (score <= 50) {
                bar.className = 'progress-bar bg-danger';
                label.textContent = 'Faible';
                label.className = 'text-danger fw-semibold';
            } else if (score === 75) {
                bar.className = 'progress-bar bg-warning';
                label.textContent = 'Moyen';
                label.className = 'text-warning fw-semibold';
            } else {
                bar.className = 'progress-bar bg-success';
                label.textContent = 'Robuste';
                label.className = 'text-success fw-semibold';
            }
        }

        (function () {
            const form = document.getElementById('register-form');
            const password = document.getElementById('password');
            const confirmation = document.getElementById('password_confirmation');
            const submitBtn = document.getElementById('submit-btn');

            form.addEventListener('submit', function (event) {
                if (password.value !== confirmation.value) {
                    event.preventDefault();
                    confirmation.setCustomValidity('Les mots de passe ne correspondent pas.');
                    confirmation.reportValidity();
                    return;
                }

                confirmation.setCustomValidity('');

                // Retour visuel pendant la soumission serveur.
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Création en cours...';
            });

            confirmation.addEventListener('input', function () {
                confirmation.setCustomValidity('');
            });
        })();
    </script>
</body>
</html>
