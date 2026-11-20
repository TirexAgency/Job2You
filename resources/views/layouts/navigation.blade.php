<nav class="offcanvas-lg offcanvas-start dashboard-sidebar border-end" tabindex="-1" id="appSidebar" aria-labelledby="appSidebarLabel">
    <div class="offcanvas-header d-lg-none border-bottom">
        <h2 class="offcanvas-title fs-6 fw-semibold" id="appSidebarLabel">Navigation</h2>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fermer le menu"></button>
    </div>
    <div class="offcanvas-body">
        <a href="{{ route('dashboard') }}" class="sidebar-brand" aria-label="Job2You, tableau de bord">
            <x-application-logo />
        </a>

        <p class="sidebar-label">{{ Auth::user()->isAdmin() ? 'Espace administration' : (Auth::user()->role === 'candidate' ? 'Espace candidat' : 'Espace personnel') }}</p>
        @if(Auth::user() && Auth::user()->role === 'candidate')
            <div class="nav flex-column gap-1">
                <a href="{{ route('candidate.dashboard') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('candidate.dashboard') ? 'active' : '' }}"><span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-speedometer2"></i></span><span>Tableau de bord</span></a>
                <a href="{{ route('candidate.profile.edit') }}#t-info" class="nav-link dashboard-nav-link {{ request()->routeIs('candidate.profile.edit') ? 'active' : '' }}"><span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-person"></i></span><span>Mon profil</span></a>
                <a href="{{ route('candidate.recommendations') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('candidate.recommendations') ? 'active' : '' }}"><span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-bullseye"></i></span><span>Mes recommandations</span></a>
                <a href="{{ route('candidate.cv') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('candidate.cv') ? 'active' : '' }}"><span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-file-earmark-text"></i></span><span>Mon CV</span></a>
                <a href="{{ route('candidate.sms') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('candidate.sms') ? 'active' : '' }}"><span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-chat-dots"></i></span><span>Mes alertes SMS</span></a>
                <a href="{{ route('candidate.subscription') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('candidate.subscription') ? 'active' : '' }}"><span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-credit-card"></i></span><span>Mon abonnement</span></a>
                <a href="{{ route('profile.edit') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"><span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-gear"></i></span><span>Paramètres</span></a>
            </div>
        @elseif(Auth::user() && Auth::user()->isAdmin())
            <div class="nav flex-column gap-1">
                <a href="{{ route('admin.dashboard') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-speedometer2"></i></span><span>Tableau de bord</span></a>
                <a href="{{ route('admin.users.index') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-people"></i></span><span>Utilisateurs</span></a>
                <a href="{{ route('admin.offers.index') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('admin.offers.*') ? 'active' : '' }}"><span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-briefcase"></i></span><span>Offres</span></a>
                <a href="{{ route('admin.sources.index') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('admin.sources.*') ? 'active' : '' }}"><span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-collection"></i></span><span>Sources</span></a>
                <a href="{{ route('admin.plans.index') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}"><span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-tags"></i></span><span>Formules</span></a>
                <a href="{{ route('admin.subscriptions.index') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}"><span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-repeat"></i></span><span>Abonnements</span></a>
                <a href="{{ route('admin.payments.index') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}"><span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-cash-coin"></i></span><span>Paiements</span></a>
                <a href="{{ route('admin.sms.index') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('admin.sms.*') ? 'active' : '' }}"><span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-chat-dots"></i></span><span>SMS</span></a>
                <a href="{{ route('admin.settings.edit') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"><span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-sliders"></i></span><span>Paramètres</span></a>
                <a href="{{ route('admin.logs.index') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}"><span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-activity"></i></span><span>Supervision</span></a>
                <a href="{{ route('profile.edit') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"><span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-gear"></i></span><span>Paramètres</span></a>
            </div>
        @else
            <div class="nav flex-column gap-1">
                <a href="{{ route('dashboard') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-speedometer2"></i></span><span>Tableau de bord</span></a>
                <a href="{{ route('jobs.index') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('jobs.*') ? 'active' : '' }}"><span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-briefcase"></i></span><span>Offres d'emploi</span></a>
                <a href="{{ route('profile.edit') }}" class="nav-link dashboard-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"><span class="sidebar-nav-mark" aria-hidden="true"><i class="bi bi-gear"></i></span><span>Paramètres</span></a>
            </div>
        @endif

        <div class="sidebar-account mt-auto">
            <div class="d-flex align-items-center gap-2">
                @if (Auth::user()->photo_url)
                    <img src="{{ Auth::user()->photo_url }}" alt="Photo de {{ Auth::user()->name }}" class="avatar-initial rounded-circle" style="object-fit: cover;">
                @else
                    <span class="avatar-initial rounded-circle bg-secondary bg-opacity-10 text-secondary d-inline-flex align-items-center justify-content-center fw-bold">{{ strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
                @endif
                <div class="small fw-semibold text-white text-truncate">{{ Auth::user()->name }}</div>
            </div>
            <div class="small text-secondary">{{ Auth::user()->role_label }}</div>
            <button type="button" class="btn btn-outline-danger w-100 text-start mt-3" data-bs-toggle="modal" data-bs-target="#logoutConfirmModal">
                <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Déconnexion
            </button>
        </div>
    </div>
</nav>

{{-- Boîte flottante de confirmation de déconnexion --}}
<div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-labelledby="logoutConfirmLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h2 class="modal-title fs-5" id="logoutConfirmLabel">Déconnexion</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body text-secondary">
                Voulez-vous vraiment vous déconnecter de Job2You ?
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger">Se déconnecter</button>
                </form>
            </div>
        </div>
    </div>
</div>
