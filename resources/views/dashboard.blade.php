<x-app-layout>
    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">{{ match ($user->role) { 'admin' => 'Espace administrateur', 'candidate' => 'Espace candidat', default => 'Espace personnel' } }}</p>
                <h1>Tableau de bord</h1>
                <p>Les informations essentielles de votre compte.</p>
            </div>
            <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary align-self-start align-self-md-center">Paramètres du compte</a>
        </header>

        <section class="app-card mb-4">
            <div class="app-card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
                <div>
                    <span class="badge text-bg-success mb-2">Compte actif</span>
                    <h2 class="app-section-title mb-1">Votre recherche commence ici</h2>
                    <p class="text-secondary mb-0">Ajoutez vos informations et vos compétences pour faciliter votre mise en relation avec les offres qui vous correspondent.</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="btn btn-primary flex-shrink-0">
                    Compléter mon profil<i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                </a>
            </div>
        </section>

        <section aria-labelledby="account-overview-title" class="mb-4">
            <h2 id="account-overview-title" class="h5 mb-3">Vue d'ensemble</h2>
            <div class="row g-3">
                <div class="col-sm-6 col-xl-4">
                    <article class="stat-card h-100">
                        <div class="card-body">
                            <p class="text-secondary small mb-1">SMS disponibles</p>
                            <p class="h3 mb-1">{{ $smsRemaining }} <span class="fs-6 text-secondary">/ {{ $smsQuota }}</span></p>
                            <p class="small text-secondary mb-0">{{ $user->sms_sent }} SMS envoyés</p>
                        </div>
                    </article>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <article class="stat-card h-100">
                        <div class="card-body">
                            <p class="text-secondary small mb-1">Formule</p>
                            <p class="h3 mb-1">{{ $user->planDisplayName() }}</p>
                            <p class="small text-secondary mb-0">Votre abonnement actuel</p>
                        </div>
                    </article>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <article class="stat-card h-100">
                        <div class="card-body">
                            <p class="text-secondary small mb-1">Téléphone</p>
                            <p class="h5 mb-1">{{ $user->phone ?: 'Non renseigné' }}</p>
                            <p class="small text-secondary mb-0">Coordonnée de votre compte</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section aria-labelledby="quick-actions-title">
            <h2 id="quick-actions-title" class="h5 mb-3">Accès rapide</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <a href="{{ route('profile.edit') }}" class="app-card d-flex align-items-center justify-content-between gap-3 h-100 p-4 text-decoration-none">
                        <span>
                            <span class="d-block fw-semibold">Paramètres</span>
                            <span class="small text-secondary">Mettre à jour mes informations</span>
                        </span>
                        <i class="bi bi-arrow-right fs-5 text-secondary" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('jobs.index') }}" class="app-card d-flex align-items-center justify-content-between gap-3 h-100 p-4 text-decoration-none">
                        <span>
                            <span class="d-block fw-semibold">Découvrir les offres</span>
                            <span class="small text-secondary">Explorer les opportunités disponibles</span>
                        </span>
                        <i class="bi bi-arrow-right fs-5 text-secondary" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </section>

        @if($user->role === 'admin')
            <section aria-labelledby="admin-actions-title" class="mt-4">
                <h2 id="admin-actions-title" class="h5 mb-3">Administration</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="{{ route('admin.users.index') }}" class="app-card d-flex align-items-center justify-content-between gap-3 h-100 p-4 text-decoration-none">
                            <span>
                                <span class="d-block fw-semibold">Gestion des utilisateurs</span>
                                <span class="small text-secondary">Créer, modifier et supprimer des utilisateurs</span>
                            </span>
                            <i class="bi bi-people fs-5 text-secondary" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('admin.users.index') }}" class="app-card d-flex align-items-center justify-content-between gap-3 h-100 p-4 text-decoration-none">
                            <span>
                                <span class="d-block fw-semibold">Nouvel utilisateur</span>
                                <span class="small text-secondary">Ajouter un utilisateur à la plateforme</span>
                            </span>
                            <i class="bi bi-person-plus fs-5 text-secondary" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </section>
        @endif
    </div>
</x-app-layout>
