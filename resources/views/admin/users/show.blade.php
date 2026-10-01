<x-app-layout>
    <div class="container-fluid px-3 px-lg-5 py-4">
        <div class="mx-auto" style="max-width: 800px;">
            <header class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <p class="text-uppercase small fw-semibold text-primary mb-1">Administration</p>
                    <h1 class="h2 fw-bold mb-1">{{ $user->name }}</h1>
                    <p class="text-secondary mb-0">Détails du compte utilisateur.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2" aria-hidden="true"></i>Modifier
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Retour</a>
                </div>
            </header>

            <section class="bg-white border rounded-3 shadow-sm p-4 p-lg-5 mb-4">
                <h2 class="h5 fw-bold mb-4">Informations personnelles</h2>
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-secondary">Nom complet</dt>
                    <dd class="col-sm-8 fw-semibold">{{ $user->name }}</dd>

                    <dt class="col-sm-4 text-secondary">Adresse email</dt>
                    <dd class="col-sm-8">{{ $user->email }}</dd>

                    <dt class="col-sm-4 text-secondary">Téléphone</dt>
                    <dd class="col-sm-8">{{ $user->phone ?: 'Non renseigné' }}</dd>

                    <dt class="col-sm-4 text-secondary">Rôle</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'recruiter' ? 'warning' : 'info') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </dd>

                    <dt class="col-sm-4 text-secondary">Statut</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'pending' ? 'warning' : 'secondary') }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </dd>

                    <dt class="col-sm-4 text-secondary">Email vérifié</dt>
                    <dd class="col-sm-8">
                        @if($user->email_verified_at)
                            <span class="text-success"><i class="bi bi-check-circle me-1" aria-hidden="true"></i>Oui, le {{ $user->email_verified_at->format('d/m/Y H:i') }}</span>
                        @else
                            <span class="text-warning"><i class="bi bi-exclamation-circle me-1" aria-hidden="true"></i>Non vérifié</span>
                        @endif
                    </dd>
                </dl>
            </section>

            <section class="bg-white border rounded-3 shadow-sm p-4 p-lg-5 mb-4">
                <h2 class="h5 fw-bold mb-4">Abonnement & SMS</h2>
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-secondary">Formule</dt>
                    <dd class="col-sm-8 fw-semibold">{{ ucfirst($user->plan) }}</dd>

                    <dt class="col-sm-4 text-secondary">Quota SMS</dt>
                    <dd class="col-sm-8">{{ $user->sms_quota }}</dd>

                    <dt class="col-sm-4 text-secondary">SMS envoyés</dt>
                    <dd class="col-sm-8">{{ $user->sms_sent }}</dd>

                    <dt class="col-sm-4 text-secondary">SMS restants</dt>
                    <dd class="col-sm-8 fw-semibold text-primary">{{ max($user->sms_quota - $user->sms_sent, 0) }}</dd>
                </dl>
            </section>

            <section class="bg-white border rounded-3 shadow-sm p-4 p-lg-5">
                <h2 class="h5 fw-bold mb-4">Informations système</h2>
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-secondary">ID utilisateur</dt>
                    <dd class="col-sm-8">{{ $user->id }}</dd>

                    <dt class="col-sm-4 text-secondary">Créé le</dt>
                    <dd class="col-sm-8">{{ $user->created_at->format('d/m/Y H:i') }}</dd>

                    <dt class="col-sm-4 text-secondary">Dernière mise à jour</dt>
                    <dd class="col-sm-8">{{ $user->updated_at->format('d/m/Y H:i') }}</dd>
                </dl>
            </section>
        </div>
    </div>
</x-app-layout>
