<x-app-layout>
    <div class="app-container">
        <div>
            <header class="app-page-header">
                <div>
                    <p class="app-eyebrow">Espace personnel</p>
                    <h1 class="h2 fw-bold mb-1">Paramètres</h1>
                    <p class="text-secondary mb-0">Gérez vos informations personnelles et la sécurité de votre compte.</p>
                </div>
                <span class="badge rounded-pill {{ $user->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }} px-3 py-2">
                    {{ $user->status === 'active' ? 'Compte actif' : $user->status_label }}
                </span>
            </header>

            <div class="row g-4">
                <aside class="col-lg-4">
                    <section class="profile-summary h-100 bg-white border rounded-3 p-4 p-lg-5" aria-labelledby="profile-summary-title">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            @if ($user->photo_url)
                                <img src="{{ $user->photo_url }}" alt="Photo de {{ $user->name }}" class="rounded-circle flex-shrink-0" style="width: 4rem; height: 4rem; object-fit: cover;">
                            @else
                                <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary fw-bold fs-3 flex-shrink-0" style="width: 4rem; height: 4rem;" aria-hidden="true">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <h2 id="profile-summary-title" class="h5 fw-bold text-break mb-1">{{ $user->name }}</h2>
                                <p class="text-secondary mb-0">{{ $user->email }}</p>
                            </div>
                        </div>

                        <dl class="mb-0">
                            <div class="border-top py-3">
                                <dt class="small text-secondary fw-normal mb-1">Rôle</dt>
                                <dd class="fw-semibold mb-0">{{ $user->role_label }}</dd>
                            </div>
                            <div class="border-top py-3">
                                <dt class="small text-secondary fw-normal mb-1">Téléphone</dt>
                                <dd class="fw-semibold mb-0">{{ $user->phone ?: 'Non renseigné' }}</dd>
                            </div>
                            <div class="border-top py-3">
                                <dt class="small text-secondary fw-normal mb-1">Adresse email</dt>
                                <dd class="mb-0">
                                    @if ($user->hasVerifiedEmail())
                                        <span class="text-success fw-semibold">Vérifiée</span>
                                    @else
                                        <span class="text-warning-emphasis fw-semibold">À vérifier</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="border-top py-3">
                                <dt class="small text-secondary fw-normal mb-1">Membre depuis</dt>
                                <dd class="fw-semibold mb-0">{{ $user->created_at?->format('d/m/Y') }}</dd>
                            </div>
                        </dl>
                    </section>
                </aside>

                <div class="col-lg-8">
                    <section class="bg-white border rounded-3 p-4 p-lg-5 mb-4" aria-labelledby="profile-information-title">
                        <h2 id="profile-information-title" class="h5 fw-bold mb-1">Informations personnelles</h2>
                        <p class="text-secondary mb-4">Votre nom et votre adresse email de connexion.</p>
                        @include('profile.partials.update-profile-information-form')
                    </section>

                    <section class="bg-white border rounded-3 p-4 p-lg-5 mb-4" aria-labelledby="profile-security-title">
                        <h2 id="profile-security-title" class="h5 fw-bold mb-1">Sécurité du compte</h2>
                        <p class="text-secondary mb-4">Choisissez un mot de passe long et unique.</p>
                        @include('profile.partials.update-password-form')
                    </section>

                    <section class="bg-white border border-danger-subtle rounded-3 p-4 p-lg-5" aria-labelledby="profile-delete-title">
                        <h2 id="profile-delete-title" class="h5 fw-bold text-danger mb-1">Suppression du compte</h2>
                        <p class="text-secondary mb-4">Cette action est définitive. Confirmez votre mot de passe pour continuer.</p>
                        @include('profile.partials.delete-user-form')
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>