<nav style="position: fixed; top: 0; left: 0; right: 0; height: 4rem; background: rgba(249, 249, 255, 0.9); backdrop-filter: blur(12px); box-shadow: 0 1px 8px rgba(0,0,0,0.04); z-index: 50; display: flex; align-items: center; justify-content: space-between; padding: 0 2rem;">
    <a href="{{ route('home') }}" style="display: flex; align-items: center; gap: 0.75rem; text-decoration: none;">
        <img src="{{ asset('logo.png') }}" alt="Job2You" style="height: 2.5rem; width: 10rem; object-fit: cover; object-position: center 46%;">
    </a>
    <div style="display: flex; align-items: center; gap: 1.5rem;">
        <a href="{{ route('home') }}" style="font-size: 0.875rem; font-weight: 500; color: var(--on-surface-variant); text-decoration: none; transition: color 0.2s; {{ request()->routeIs('home') ? 'color: var(--primary);' : '' }}">Accueil</a>
        <a href="" style="font-size: 0.875rem; font-weight: 500; color: var(--on-surface-variant); text-decoration: none; transition: color 0.2s; {{ request()->routeIs('offers.*') ? 'color: var(--primary);' : '' }}">Offres</a>
        <a href="" style="font-size: 0.875rem; font-weight: 500; color: var(--on-surface-variant); text-decoration: none; transition: color 0.2s;">Tarifs</a>
        <a href="" style="font-size: 0.875rem; font-weight: 500; color: var(--on-surface-variant); text-decoration: none; transition: color 0.2s;">Comment ça marche</a>
    </div>
    <div style="display: flex; align-items: center; gap: 0.75rem;">
        @auth
            <a href="{{ route('dashboard') }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem; background: var(--primary); color: var(--on-primary); font-size: 0.875rem; font-weight: 500; padding: 0.5rem 1rem; border-radius: 0.5rem; border: none; cursor: pointer; transition: all 0.2s; text-decoration: none;">
                <i class="bi bi-speedometer2"></i>
                <span>Mon espace</span>
            </a>
        @else
            <a href="{{ route('login') }}" style="font-size: 0.875rem; font-weight: 500; color: var(--on-surface-variant); text-decoration: none; padding: 0.5rem 0.75rem; transition: color 0.2s;">Connexion</a>
            <a href="{{ route('register') }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem; background: var(--primary); color: var(--on-primary); font-size: 0.875rem; font-weight: 500; padding: 0.5rem 1rem; border-radius: 0.5rem; border: none; cursor: pointer; transition: all 0.2s; text-decoration: none;">
                <i class="bi bi-person-plus"></i>
                <span>Créer un compte</span>
            </a>
        @endauth
    </div>
</nav>
