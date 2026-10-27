<x-app-layout>
    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Administration</p>
                <h1 class="h2 fw-bold mb-1">Tableau de bord</h1>
                <p class="text-secondary mb-0">Vue d’ensemble de la plateforme et de ses performances.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary"><i class="bi bi-plus-circle me-1"></i>Nouvel utilisateur</a>
            </div>
        </header>

        <!-- Statistiques principales -->
        <section class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-3">
                            <i class="bi bi-people-fill fs-3"></i>
                        </div>
                        <div>
                            <p class="text-secondary small mb-1">Utilisateurs</p>
                            <p class="h3 fw-bold mb-0">{{ number_format($stats['users']['total'], 0, ',', ' ') }}</p>
                            <p class="small text-success mb-0">+{{ $stats['users']['new'] }} ce mois</p>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <small class="text-muted">{{ $stats['users']['candidates'] }} candidats · {{ $stats['users']['admins'] }} admins · {{ $stats['users']['pending'] }} en attente</small>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon bg-success bg-opacity-10 text-success rounded-3">
                            <i class="bi bi-briefcase-fill fs-3"></i>
                        </div>
                        <div>
                            <p class="text-secondary small mb-1">Offres d'emploi</p>
                            <p class="h3 fw-bold mb-0">{{ number_format($stats['offers']['total'], 0, ',', ' ') }}</p>
                            <p class="small text-success mb-0">+{{ $stats['offers']['new'] }} cette semaine</p>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <small class="text-muted">{{ $stats['offers']['active'] }} actives · {{ $stats['offers']['expired'] }} expirées</small>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-3">
                            <i class="bi bi-graph-up-arrow fs-3"></i>
                        </div>
                        <div>
                            <p class="text-secondary small mb-1">Matchings</p>
                            <p class="h3 fw-bold mb-0">{{ number_format($stats['matches']['total'], 0, ',', ' ') }}</p>
                            <p class="small text-success mb-0">+{{ $stats['matches']['recent'] }} aujourd'hui</p>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <small class="text-muted">{{ $stats['matches']['above_threshold'] }} au-dessus du seuil</small>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon bg-info bg-opacity-10 text-info rounded-3">
                            <i class="bi bi-credit-card-fill fs-3"></i>
                        </div>
                        <div>
                            <p class="text-secondary small mb-1">Abonnements</p>
                            <p class="h3 fw-bold mb-0">{{ $stats['subscriptions']['active'] }}</p>
                            <p class="small text-success mb-0">Chiffre mois: {{ number_format($stats['subscriptions']['revenue_month'], 0, ',', ' ') }} Ar</p>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <small class="text-muted">{{ count($stats['subscriptions']['by_plan']) }} formules</small>
                    </div>
                </div>
            </div>
        </section>

        <div class="row g-3 mb-4">
            <!-- Sources par offres -->
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <h2 class="h5 fw-bold mb-0">Top sources par offres</h2>
                            <a href="{{ route('admin.sources.index') }}" class="btn btn-sm btn-outline-secondary">Voir tout</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Source</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-end">Offres actives</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['offers']['by_source'] as $source)
                                        <tr>
                                            <td>
                                                <span class="fw-medium">{{ $source->name }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $source->collector_key }}</span>
                                            </td>
                                            <td class="text-end fw-semibold">{{ $source->offers_count }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">Aucune source</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Abonnements par plan -->
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <h2 class="h5 fw-bold mb-0">Répartition par plan</h2>
                            <a href="{{ route('admin.plans.index') }}" class="btn btn-sm btn-outline-secondary">Gérer</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
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
                                            <td>
                                                <span class="badge bg-{{ $plan->name === 'free' ? 'secondary' : 'primary' }} bg-opacity-10 text-{{ $plan->name === 'free' ? 'secondary' : 'primary' }}">
                                                    {{ ucfirst($plan->name) }}
                                                </span>
                                            </td>
                                            <td class="text-center">{{ $plan->price > 0 ? number_format($plan->price, 0, ',', ' ') . ' Ar' : 'Gratuit' }}</td>
                                            <td class="text-center">{{ $plan->sms_quota }}</td>
                                            <td class="text-end fw-semibold">{{ $plan->subscriptions_count }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">Aucun plan</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activité récente -->
        <div class="row g-3">
            <!-- Nouveaux utilisateurs -->
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <h2 class="h5 fw-bold mb-0">Nouveaux utilisateurs</h2>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">Voir tout</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($recentUsers as $user)
                                <div class="list-group-item px-3 py-3 border-0 border-bottom">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <div class="fw-medium text-truncate">{{ $user->name }}</div>
                                            <div class="small text-secondary text-truncate">{{ $user->email }}</div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-{{ ['admin'=>'danger','candidate'=>'primary','recruiter'=>'info'][$user->role] ?? 'secondary' }} bg-opacity-10 text-{{ ['admin'=>'danger','candidate'=>'primary','recruiter'=>'info'][$user->role] ?? 'secondary' }} text-uppercase">{{ $user->role }}</span>
                                            <span class="badge bg-{{ ['active'=>'success','pending'=>'warning','inactive'=>'secondary','suspended'=>'danger'][$user->status] ?? 'secondary' }} bg-opacity-10 text-{{ ['active'=>'success','pending'=>'warning','inactive'=>'secondary','suspended'=>'danger'][$user->status] ?? 'secondary' }}">{{ $user->status }}</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item text-center text-muted py-4">Aucun utilisateur</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dernières offres -->
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <h2 class="h5 fw-bold mb-0">Dernières offres</h2>
                            <a href="{{ route('admin.offers.index') }}" class="btn btn-sm btn-outline-secondary">Voir tout</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($recentOffers as $offer)
                                <div class="list-group-item px-3 py-3 border-0 border-bottom">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-briefcase-fill"></i>
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <div class="fw-medium text-truncate">{{ $offer->title }}</div>
                                            <div class="small text-secondary">{{ $offer->source->name ?? 'Source inconnue' }}</div>
                                        </div>
                                        <span class="badge bg-{{ ['active'=>'success','expired'=>'warning','archived'=>'secondary'][$offer->status] ?? 'secondary' }} bg-opacity-10 text-{{ ['active'=>'success','expired'=>'warning','archived'=>'secondary'][$offer->status] ?? 'secondary' }}">{{ ucfirst($offer->status) }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item text-center text-muted py-4">Aucune offre</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Derniers paiements -->
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <h2 class="h5 fw-bold mb-0">Derniers paiements</h2>
                            <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-secondary">Voir tout</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($recentPayments as $payment)
                                <div class="list-group-item px-3 py-3 border-0 border-bottom">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-cash-coin"></i>
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <div class="fw-medium">{{ $payment->user->name ?? 'Utilisateur' }}</div>
                                            <div class="small text-secondary">{{ $payment->user->email ?? '' }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold text-success">{{ number_format($payment->amount, 0, ',', ' ') }} Ar</div>
                                            <div class="small text-muted">{{ $payment->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item text-center text-muted py-4">Aucun paiement</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>