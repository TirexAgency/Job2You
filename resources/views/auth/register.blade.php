<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inscription - Job2You</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
        }
        .password-wrapper {
            position: relative;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0"><i class="bi bi-briefcase-fill"></i> Job2You - Inscription</h4>
                    </div>
                    <div class="card-body p-4">

                        <!-- Message de succès -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Erreurs de validation -->
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <strong>Erreurs dans le formulaire :</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <!-- Nom -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Adresse email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Téléphone -->
                            <div class="mb-3">
                                <label for="phone" class="form-label">Numéro de téléphone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone') }}"
                                       placeholder="+261 34 12 345 67" required>
                                <div class="form-text">Format Madagascar (ex: +261 34 12 345 67 ou 034 12 345 67)</div>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Mot de passe -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                                <div class="password-wrapper">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password" required>
                                    <i class="bi bi-eye-slash password-toggle" id="togglePassword"></i>
                                </div>
                                <div class="form-text">8 caractères minimum, avec majuscule, minuscule, chiffre et symbole</div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirmation mot de passe -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                                <div class="password-wrapper">
                                    <input type="password" class="form-control"
                                           id="password_confirmation" name="password_confirmation" required>
                                    <i class="bi bi-eye-slash password-toggle" id="togglePasswordConfirmation"></i>
                                </div>
                            </div>

                            <!-- Bouton -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-person-plus"></i> Créer mon compte
                                </button>
                            </div>
                        </form>

                        <hr>
                        <p class="text-center mb-0">
                            Déjà un compte ? <a href="{{ route('login') }}">Se connecter</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        
        document.querySelectorAll('[id^=togglePassword]').forEach(function (el) {
            el.addEventListener('click', function () {
                const field = el.id === 'togglePasswordConfirmation'
                    ? document.getElementById('password_confirmation')
                    : document.getElementById('password');
                field.type = field.type === 'password' ? 'text' : 'password';
                el.classList.toggle('bi-eye');
                el.classList.toggle('bi-eye-slash');
            });
        });
    </script>
</body>
</html>
