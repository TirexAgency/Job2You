{{--
    En-tête public Job2You (pages connexion / inscription).
    S'adapte automatiquement à l'état de connexion de l'utilisateur.
--}}
<nav class="navbar navbar-expand-lg fixed-top job2you-public-navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            @include('partials.logo-mark')
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNavbarNav" aria-controls="publicNavbarNav" aria-expanded="false" aria-label="Ouvrir le menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="publicNavbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('offers.*') ? 'active' : '' }}" href="{{ route('offers.index') }}">Offres</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pricing') ? 'active' : '' }}" href="{{ route('pricing') }}">Tarifs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('how-it-works') ? 'active' : '' }}" href="{{ route('how-it-works') }}">Comment ça marche</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <i class="bi bi-speedometer2 me-1"></i>
                        <span>Mon espace</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="nav-link px-3 {{ request()->routeIs('login') ? 'active' : '' }}">Connexion</a>
                    <a href="{{ route('register') }}" class="btn btn-primary {{ request()->routeIs('register') ? 'active' : '' }}">
                        <i class="bi bi-person-plus me-1"></i>
                        <span>Créer un compte</span>
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
