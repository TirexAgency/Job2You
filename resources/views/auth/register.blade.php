<x-auth-layout
    title="Créer un compte"
    subtitle="Un profil, un score de compatibilité, des alertes SMS."
    aside-title="Votre profil fait le tri à votre place."
    aside-text="Renseignez vos compétences et vos préférences une fois. Chaque nouvelle offre est comparée à votre profil, puis vous recevez celles qui passent le seuil."
    :aside-points="[
        'Profil gratuit et modifiable à tout moment',
        'Alertes SMS dès qu\'une offre correspond',
        'CV intégré pour postuler depuis la plateforme',
    ]"
>
    @if (session('success'))
        <div class="alert alert-success mb-3" role="status">
            <i class="bi bi-check-circle" aria-hidden="true"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger mb-3" role="alert">
            <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-forms.form action="{{ route('register') }}" id="register-form" class="needs-validation" novalidate>
        <x-forms.field name="name" label="Nom complet" type="text" placeholder="Hery Randria" required autocomplete="name" :error="$errors->first('name')" />
        <x-forms.field name="phone" label="Téléphone" type="tel" placeholder="+261 34 00 000 00" required autocomplete="tel" :error="$errors->first('phone')" help="Format Madagascar : +261 34 12 345 67 ou 034 12 345 67" />
        <x-forms.field name="email" label="Email" type="email" placeholder="nom@exemple.com" required autocomplete="email" :error="$errors->first('email')" />
        <x-forms.field name="password" label="Mot de passe" type="password" placeholder="Votre mot de passe" required autocomplete="new-password" :error="$errors->first('password')" help="8 caractères minimum, une majuscule, un chiffre et un caractère spécial" />
        <x-forms.field name="password_confirmation" label="Confirmation" type="password" placeholder="Répétez le mot de passe" required autocomplete="new-password" :error="$errors->first('password_confirmation')" />

        <div class="form-check mb-4">
            <input type="checkbox" class="form-check-input" id="terms" name="terms" required @checked(old('terms'))>
            <label class="form-check-label" for="terms">
                J'accepte les <a href="{{ route('terms') }}" target="_blank">conditions d'utilisation</a>
                et la <a href="{{ route('privacy') }}" target="_blank">politique de confidentialité</a> de Job2You.
            </label>
            @error('terms')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <x-forms.button label="Créer mon compte" variant="primary" size="lg" block id="submit-btn" />
    </x-forms.form>

    <div class="auth-switch">
        Vous avez déjà un compte ?
        <a href="{{ route('login') }}">Se connecter</a>
    </div>
@push('scripts')
<script>
    (function () {
        const form = document.getElementById('register-form');
        const password = document.getElementById('password');
        const confirmation = document.getElementById('password_confirmation');
        const submitBtn = document.getElementById('submit-btn');

        if (!form || !password || !confirmation) {
            return;
        }

        form.addEventListener('submit', function (event) {
            if (password.value !== confirmation.value) {
                event.preventDefault();
                confirmation.setCustomValidity('Les mots de passe ne correspondent pas.');
                confirmation.reportValidity();
                return;
            }

            confirmation.setCustomValidity('');

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Création…';
            }
        });

        confirmation.addEventListener('input', function () {
            confirmation.setCustomValidity('');
        });
    })();
</script>
@endpush
</x-auth-layout>


