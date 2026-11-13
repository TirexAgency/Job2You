<x-auth-layout
    title="Vérifiez votre adresse email"
    subtitle="Un lien de vérification a été envoyé à votre adresse. Cliquez dessus pour activer votre compte."
    aside-title="Une dernière étape avant les offres."
    aside-text="La vérification de l'email protège votre compte et garantit que les alertes arrivent au bon endroit."
>
    @if (session('success'))
        <div class="alert alert-success mb-3" role="status">
            <i class="bi bi-check-circle" aria-hidden="true"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('message'))
        <div class="alert alert-success mb-3" role="status">
            <i class="bi bi-check-circle" aria-hidden="true"></i>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    @if (session('resent'))
        <div class="alert alert-success mb-3" role="status">
            <i class="bi bi-check-circle" aria-hidden="true"></i>
            <span>{{ session('resent') }}</span>
        </div>
    @endif

    @auth
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-forms.button label="Renvoyer le lien" variant="primary" size="lg" block icon="bi bi-send" />
        </form>

        <div class="auth-switch">
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-link p-0">Se déconnecter</button>
            </form>
        </div>
    @else
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-forms.field name="email" label="Adresse email" type="email" placeholder="nom@exemple.com" required autocomplete="email" />
            <x-forms.button label="Renvoyer le lien" variant="primary" size="lg" block icon="bi bi-send" />
        </form>

        <div class="auth-switch">
            <a href="{{ route('login') }}">Se connecter</a>
        </div>
    @endauth
</x-auth-layout>
