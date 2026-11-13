<x-auth-layout
    title="Confirmer le mot de passe"
    subtitle="Cette zone est sécurisée. Confirmez votre mot de passe pour continuer."
    aside-title="Une vérification avant de continuer."
    aside-text="La confirmation protège les actions sensibles de votre compte."
>
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

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <x-forms.field name="password" label="Mot de passe" type="password" placeholder="Votre mot de passe" required autofocus autocomplete="current-password" :error="$errors->first('password')" />

        <x-forms.button label="Confirmer" variant="primary" size="lg" block />
    </form>
</x-auth-layout>
