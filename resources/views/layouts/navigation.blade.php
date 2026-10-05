<nav class="offcanvas-lg offcanvas-start dashboard-sidebar border-end" tabindex="-1" id="appSidebar" aria-labelledby="appSidebarLabel">
    <div class="offcanvas-header d-lg-none border-bottom">
        <h2 class="offcanvas-title fs-6 fw-semibold" id="appSidebarLabel">Navigation</h2>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fermer le menu"></button>
    </div>
    <div class="offcanvas-body">
        <a href="{{ route('dashboard') }}" class="sidebar-brand" aria-label="Job2You, tableau de bord">
            <x-application-logo />
        </a>

        <p class="sidebar-label">Espace personnel</p>
        <div class="nav nav-pills flex-column gap-1">
            <a href="{{ route('dashboard') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" @if (request()->routeIs('dashboard')) aria-current="page" @endif>
                <span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-speedometer2"></i></span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('jobs.index') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('jobs.*') ? 'active' : '' }}" @if (request()->routeIs('jobs.*')) aria-current="page" @endif>
                <span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-briefcase"></i></span>
                <span>Offres d'emploi</span>
            </a>
            <a href="{{ route('profile.edit') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" @if (request()->routeIs('profile.*')) aria-current="page" @endif>
                <span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-gear"></i></span>
                <span>Paramètres</span>
            </a>
        </div>

        @if(Auth::user() && Auth::user()->role === 'candidate')
            <p class="sidebar-label mt-4">Candidat</p>
            <div class="nav nav-pills flex-column gap-1">
                <a href="{{ route('candidate.dashboard') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('candidate.dashboard') ? 'active' : '' }}" @if (request()->routeIs('candidate.dashboard')) aria-current="page" @endif>
                    <span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-graph-up"></i></span>
                    <span>Aperçu candidat</span>
                </a>
                <a href="{{ route('candidate.profile.edit') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('candidate.profile.*') ? 'active' : '' }}" @if (request()->routeIs('candidate.profile.*')) aria-current="page" @endif>
                    <span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-card-checklist"></i></span>
                    <span>Mon profil candidat</span>
                </a>
            </div>
        @endif

        @if(Auth::user() && Auth::user()->isAdmin())
            <p class="sidebar-label mt-4">Administration</p>
            <div class="nav nav-pills flex-column gap-1">
                <a href="{{ route('admin.users.index') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" @if (request()->routeIs('admin.users.*')) aria-current="page" @endif>
                    <span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-people"></i></span>
                    <span>Utilisateurs</span>
                </a>
            </div>
        @endif

        <div class="sidebar-account mt-auto">
            <div class="d-flex align-items-center gap-2">
                @if (Auth::user()->photo_path)
                    <img src="{{ asset('storage/'.Auth::user()->photo_path) }}" alt="Photo de {{ Auth::user()->name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                @else
                    <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">{{ strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
                @endif
                <div class="small fw-semibold text-truncate">{{ Auth::user()->name }}</div>
            </div>
            <div class="small text-secondary">{{ ucfirst(Auth::user()->role ?? 'Utilisateur') }}</div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 text-start" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')">
                    <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Déconnexion
                </button>
            </form>
        </div>
    </div>
</nav>
