<x-auth-layout
    title="Mot de passe oublié"
    subtitle="Indiquez votre email pour recevoir un lien de réinitialisation."
    aside-title="Un lien, un nouveau mot de passe."
    aside-text="La réinitialisation se fait par email. Le lien expire après 60 minutes."
>
    @if (session('status'))
        <div class="alert alert-success mb-3" role="status">
            <i class="bi bi-check-circle" aria-hidden="true"></i>
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

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <x-forms.field name="email" label="Adresse email" type="email" placeholder="nom@exemple.com" required autofocus autocomplete="email" :error="$errors->first('email')" />

        <x-forms.button label="Envoyer le lien" variant="primary" size="lg" block />
    </form>

    <div class="auth-switch">
        <a href="{{ route('login') }}">Retour à la connexion</a>
    </div>
</x-auth-layout>
