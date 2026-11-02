<nav class="navbar navbar-expand-lg public-navbar">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}" aria-label="Job2You, accueil">
            <img src="{{ asset('logo.png') }}" alt="Job2You">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNavbar" aria-controls="publicNavbar" aria-expanded="false" aria-label="Ouvrir la navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="publicNavbar">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link public-nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif>Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link public-nav-link {{ request()->routeIs('offers.*') ? 'active' : '' }}" href="{{ route('offers.index') }}" @if(request()->routeIs('offers.*')) aria-current="page" @endif>Offres</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link public-nav-link {{ request()->routeIs('pricing') ? 'active' : '' }}" href="{{ route('pricing') }}" @if(request()->routeIs('pricing')) aria-current="page" @endif>Tarifs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link public-nav-link {{ request()->routeIs('how-it-works') ? 'active' : '' }}" href="{{ route('how-it-works') }}" @if(request()->routeIs('how-it-works')) aria-current="page" @endif>Comment ça marche</a>
                </li>
            </ul>

            <div class="public-nav-actions d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <i class="bi bi-speedometer2 me-1" aria-hidden="true"></i>Mon espace
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-link">Connexion</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">
                        <i class="bi bi-person-plus me-1" aria-hidden="true"></i>Créer un compte
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
