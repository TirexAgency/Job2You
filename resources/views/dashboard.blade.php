<x-app-layout>
    <div class="container-fluid px-3 px-lg-5 py-4">
        <div class="mx-auto" style="max-width: 1200px;">
            <header class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <p class="text-uppercase small fw-semibold text-primary mb-1">Espace candidat</p>
                    <h1 class="h2 fw-bold mb-1">Bonjour {{ $user->name }}</h1>
                    <p class="text-secondary mb-0">Retrouvez ici les informations essentielles de votre compte.</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="btn btn-primary px-4 py-2">Compléter mon profil</a>
            </header>

            <section class="bg-white border-start border-4 border-primary rounded-3 shadow-sm p-4 p-lg-5 mb-4">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <span class="badge text-bg-success mb-3">Compte actif</span>
                        <h2 class="h4 fw-bold mb-2">Votre recherche commence ici</h2>
                        <p class="text-secondary mb-0">Ajoutez vos informations et vos compétences pour faciliter votre mise en relation avec les offres qui vous correspondent.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">Gérer mon profil <span aria-hidden="true">&rarr;</span></a>
                    </div>
                </div>
            </section>

            <section aria-labelledby="account-overview-title" class="mb-4">
                <h2 id="account-overview-title" class="h5 fw-bold mb-3">Vue d'ensemble</h2>
                <div class="row g-3">
                    <div class="col-sm-6 col-xl-4">
                        <article class="h-100 bg-white border rounded-3 p-4">
                            <p class="small text-secondary mb-2">SMS disponibles</p>
                            <p class="h3 fw-bold mb-1">{{ $smsRemaining }} <span class="fs-6 fw-normal text-secondary">/ {{ $smsQuota }}</span></p>
                            <p class="small text-secondary mb-0">{{ $user->sms_sent }} SMS envoyés</p>
                        </article>
                    </div>
                    <div class="col-sm-6 col-xl-4">
                        <article class="h-100 bg-white border rounded-3 p-4">
                            <p class="small text-secondary mb-2">Formule</p>
                            <p class="h3 fw-bold mb-1">{{ $user->plan === 'free' ? 'Gratuite' : ucfirst($user->plan) }}</p>
                            <p class="small text-secondary mb-0">Votre abonnement actuel</p>
                        </article>
                    </div>
                    <div class="col-sm-6 col-xl-4">
                        <article class="h-100 bg-white border rounded-3 p-4">
                            <p class="small text-secondary mb-2">Téléphone</p>
                            <p class="h5 fw-bold mb-1">{{ $user->phone ?: 'Non renseigné' }}</p>
                            <p class="small text-secondary mb-0">Coordonnée de votre compte</p>
                        </article>
                    </div>
                </div>
            </section>

            <section aria-labelledby="quick-actions-title">
                <h2 id="quick-actions-title" class="h5 fw-bold mb-3">Accès rapide</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="{{ route('profile.edit') }}" class="d-flex align-items-center justify-content-between gap-3 h-100 bg-white border rounded-3 p-4 text-decoration-none text-dark">
                            <span>
                                <span class="d-block fw-semibold">Mon profil</span>
                                <span class="small text-secondary">Mettre à jour mes informations</span>
                            </span>
                            <span class="fs-4 text-primary" aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('jobs.index') }}" class="d-flex align-items-center justify-content-between gap-3 h-100 bg-white border rounded-3 p-4 text-decoration-none text-dark">
                            <span>
                                <span class="d-block fw-semibold">Découvrir les offres</span>
                                <span class="small text-secondary">Explorer les opportunités disponibles</span>
                            </span>
                            <span class="fs-4 text-primary" aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
