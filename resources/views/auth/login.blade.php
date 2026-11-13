<x-auth-layout
    title="Connexion"
    subtitle="Accédez à votre espace Job2You."
    :aside-points="[
        'Score de compatibilité sur chaque offre',
        'Alertes SMS à la publication',
        'Profil candidat et CV au même endroit',
    ]"
>
    @if (session('success'))
        <div class="alert alert-success mb-3" role="status">
            <i class="bi bi-check-circle" aria-hidden="true"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-info mb-3" role="status">
            <i class="bi bi-info-circle" aria-hidden="true"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger mb-3" role="alert">
            <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
            <div>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <x-forms.form action="{{ route('login') }}" class="needs-validation" novalidate>
        <x-forms.field name="email" label="Email ou téléphone" type="text" placeholder="nom@exemple.com ou +261 34 00 000 00" required autofocus autocomplete="username" :error="$errors->first('email')" />
        <x-forms.field name="password" label="Mot de passe" type="password" placeholder="Votre mot de passe" required autocomplete="current-password" :error="$errors->first('password')" />

        <div class="auth-links">
            <a href="{{ route('password.request') }}">Mot de passe oublié</a>
        </div>

        <div class="form-check mb-4">
            <input type="checkbox" class="form-check-input" name="remember" value="1" id="remember" @checked(old('remember'))>
            <label class="form-check-label" for="remember">Se souvenir de moi</label>
        </div>

        <x-forms.button label="Se connecter" variant="primary" size="lg" block />
    </x-forms.form>

    <div class="auth-switch">
        Pas encore de compte ?
        <a href="{{ route('register') }}">Créer un compte</a>
    </div>
</x-auth-layout>
