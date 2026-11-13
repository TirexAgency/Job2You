<x-app-layout>
    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Administration</p>
                <h1>Tableau de bord</h1>
                <p class="text-secondary mb-0">Vue d'ensemble de la plateforme et de ses performances.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">Nouvel utilisateur</a>
            </div>
        </header>

        <section class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <p class="text-secondary small mb-1">Utilisateurs</p>
                        <p class="h3 mb-1">{{ number_format($stats['users']['total'], 0, ',', ' ') }}</p>
                        <p class="small text-secondary mb-0">+{{ $stats['users']['new'] }} ce mois</p>
                    </div>
                    <div class="card-footer border-0 pt-0">
                        <small class="text-secondary">{{ $stats['users']['candidates'] }} candidats · {{ $stats['users']['admins'] }} admins · {{ $stats['users']['pending'] }} en attente</small>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <p class="text-secondary small mb-1">Offres d'emploi</p>
                        <p class="h3 mb-1">{{ number_format($stats['offers']['total'], 0, ',', ' ') }}</p>
                        <p class="small text-secondary mb-0">+{{ $stats['offers']['new'] }} cette semaine</p>
                    </div>
                    <div class="card-footer border-0 pt-0">
                        <small class="text-secondary">{{ $stats['offers']['active'] }} actives · {{ $stats['offers']['expired'] }} expirées</small>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <p class="text-secondary small mb-1">Matchings</p>
                        <p class="h3 mb-1">{{ number_format($stats['matches']['total'], 0, ',', ' ') }}</p>
                        <p class="small text-secondary mb-0">+{{ $stats['matches']['recent'] }} aujourd'hui</p>
                    </div>
                    <div class="card-footer border-0 pt-0">
                        <small class="text-secondary">{{ $stats['matches']['above_threshold'] }} au-dessus du seuil</small>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <p class="text-secondary small mb-1">Abonnements</p>
                        <p class="h3 mb-1">{{ $stats['subscriptions']['active'] }}</p>
                        <p class="small text-secondary mb-0">{{ number_format($stats['subscriptions']['revenue_month'], 0, ',', ' ') }} Ar ce mois</p>
                    </div>
                    <div class="card-footer border-0 pt-0">
                        <small class="text-secondary">{{ count($stats['subscriptions']['by_plan']) }} formules</small>
                    </div>
                </div>
            </div>
        </section>

        <div class="row g-3 mb-4">
            <div class="col-xl-6">
                <div class="app-card h-100">
                    <div class="app-card-header">
                        <h2 class="app-section-title">Top sources par offres</h2>
                        <a href="{{ route('admin.sources.index') }}" class="btn btn-sm btn-outline-secondary">Voir tout</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Source</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-end">Offres actives</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['offers']['by_source'] as $source)
                                    <tr>
                                        <td><span class="fw-medium">{{ $source->name }}</span></td>
                                        <td class="text-center"><span class="badge">{{ $source->collector_key }}</span></td>
                                        <td class="text-end fw-medium">{{ $source->offers_count }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-secondary py-4">Aucune source</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="app-card h-100">
                    <div class="app-card-header">
                        <h2 class="app-section-title">Répartition par plan</h2>
                        <a href="{{ route('admin.plans.index') }}" class="btn btn-sm btn-outline-secondary">Gérer</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Plan</th>
                                    <th class="text-center">Prix</th>
                                    <th class="text-center">SMS</th>
                                    <th class="text-end">Abonnés</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['subscriptions']['by_plan'] as $plan)
                                    <tr>
                                        <td><span class="badge">{{ ucfirst($plan->name) }}</span></td>
                                        <td class="text-center">{{ $plan->price > 0 ? number_format($plan->price, 0, ',', ' ') . ' Ar' : 'Gratuit' }}</td>
                                        <td class="text-center">{{ $plan->sms_quota }}</td>
                                        <td class="text-end fw-medium">{{ $plan->subscriptions_count }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-secondary py-4">Aucun plan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-4">
                <div class="app-card h-100">
                    <div class="app-card-header">
                        <h2 class="app-section-title">Nouveaux utilisateurs</h2>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">Voir tout</a>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($recentUsers as $user)
                            <div class="list-group-item">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-sm bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-person" aria-hidden="true"></i>
                                    </div>
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="fw-medium text-truncate">{{ $user->name }}</div>
                                        <div class="small text-secondary text-truncate">{{ $user->email }}</div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge">{{ $user->role }}</span>
                                        <span class="badge">{{ $user->status }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-secondary py-4">Aucun utilisateur</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="app-card h-100">
                    <div class="app-card-header">
                        <h2 class="app-section-title">Dernières offres</h2>
                        <a href="{{ route('admin.offers.index') }}" class="btn btn-sm btn-outline-secondary">Voir tout</a>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($recentOffers as $offer)
                            <div class="list-group-item">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-sm bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-briefcase" aria-hidden="true"></i>
                                    </div>
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="fw-medium text-truncate">{{ $offer->title }}</div>
                                        <div class="small text-secondary">{{ $offer->source->name ?? 'Source inconnue' }}</div>
                                    </div>
                                    <span class="badge">{{ ucfirst($offer->status) }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-secondary py-4">Aucune offre</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="app-card h-100">
                    <div class="app-card-header">
                        <h2 class="app-section-title">Derniers paiements</h2>
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-secondary">Voir tout</a>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($recentPayments as $payment)
                            <div class="list-group-item">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-sm bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-cash-coin" aria-hidden="true"></i>
                                    </div>
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="fw-medium text-truncate">{{ $payment->user->name ?? 'Utilisateur' }}</div>
                                        <div class="small text-secondary text-truncate">{{ $payment->user->email ?? '' }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-medium">{{ number_format($payment->amount, 0, ',', ' ') }} Ar</div>
                                        <div class="small text-secondary">{{ $payment->created_at->locale('fr')->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-secondary py-4">Aucun paiement</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
