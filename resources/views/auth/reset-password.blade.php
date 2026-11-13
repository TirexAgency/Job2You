<x-auth-layout
    title="Nouveau mot de passe"
    subtitle="Choisissez un mot de passe pour votre compte."
    aside-title="Reprenez la main sur votre compte."
    aside-text="Un mot de passe robuste contient au moins 8 caractères, une majuscule, un chiffre et un caractère spécial."
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

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-forms.field name="email" label="Adresse email" type="email" :value="$request->email" required autofocus autocomplete="email" :error="$errors->first('email')" />
        <x-forms.field name="password" label="Nouveau mot de passe" type="password" placeholder="Votre mot de passe" required autocomplete="new-password" :error="$errors->first('password')" help="8 caractères minimum, une majuscule, un chiffre et un caractère spécial" />
        <x-forms.field name="password_confirmation" label="Confirmation" type="password" placeholder="Répétez le mot de passe" required autocomplete="new-password" />

        <x-forms.button label="Réinitialiser" variant="primary" size="lg" block />
    </form>

    <div class="auth-switch">
        <a href="{{ route('login') }}">Retour à la connexion</a>
    </div>
</x-auth-layout>
