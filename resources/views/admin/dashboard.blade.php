<x-app-layout>
    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Administration</p>
                <h1>Tableau de bord</h1>
                <p class="text-secondary mb-0">Vue d'ensemble de la plateforme et de ses performances.</p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-3">
                <span class="small text-secondary">Données au {{ $refreshedAt->format('d/m/Y à H:i') }}</span>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-clockwise me-1" aria-hidden="true"></i>Actualiser
                </a>
            </div>
        </header>

        <section class="row g-3 mb-3" aria-label="Indicateurs clés">
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex flex-column">
                        <p class="text-secondary small mb-1">Utilisateurs</p>
                        <p class="h3 mb-1">{{ number_format($stats['users']['total'], 0, ',', ' ') }}</p>
                        <p class="small text-secondary mb-0">+{{ $stats['users']['new'] }} ce mois</p>
                        <p class="small text-secondary mb-0 mt-auto pt-3">{{ $stats['users']['candidates'] }} candidats · {{ $stats['users']['admins'] }} admins · {{ $stats['users']['pending'] }} en attente</p>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex flex-column">
                        <p class="text-secondary small mb-1">Offres d'emploi</p>
                        <p class="h3 mb-1">{{ number_format($stats['offers']['total'], 0, ',', ' ') }}</p>
                        <p class="small text-secondary mb-0">+{{ $stats['offers']['new'] }} cette semaine</p>
                        <p class="small text-secondary mb-0 mt-auto pt-3">{{ $stats['offers']['active'] }} actives · {{ $stats['offers']['expired'] }} expirées</p>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex flex-column">
                        <p class="text-secondary small mb-1">Revenus confirmés</p>
                        <p class="h3 mb-1">{{ number_format((float) $stats['payments']['revenue_total'], 0, ',', ' ') }} Ar</p>
                        <p class="small text-secondary mb-0">{{ $stats['payments']['paid'] }} paiement(s) payé(s)</p>
                        <p class="small text-secondary mb-0 mt-auto pt-3">{{ $stats['payments']['failed'] }} échec(s) · {{ $stats['payments']['refunded'] }} remboursé(s)</p>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex flex-column">
                        <p class="text-secondary small mb-1">Abonnements actifs</p>
                        <p class="h3 mb-1">{{ $stats['subscriptions']['active'] }}</p>
                        <p class="small text-secondary mb-0">{{ number_format($stats['subscriptions']['revenue_month'], 0, ',', ' ') }} Ar ce mois</p>
                        <p class="small text-secondary mb-0 mt-auto pt-3">{{ count($stats['subscriptions']['by_plan']) }} formules</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="admin-watch-strip mb-4" aria-label="Points à surveiller">
            <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="admin-watch-item">
                <span class="admin-watch-label">Paiements en attente</span>
                <span class="admin-watch-value">{{ $stats['payments']['pending'] }}</span>
                <span class="admin-watch-meta">{{ $stats['payments']['failed'] }} échec(s) · {{ $stats['payments']['refunded'] }} remboursé(s)</span>
            </a>
            <a href="{{ route('admin.sms.index', ['status' => 'failed']) }}" class="admin-watch-item">
                <span class="admin-watch-label">SMS échoués ce mois</span>
                <span class="admin-watch-value">{{ $stats['sms']['failed_month'] }}</span>
                <span class="admin-watch-meta">{{ number_format($stats['sms']['sent_month'], 0, ',', ' ') }} envoyé(s) ce mois</span>
            </a>
            <div class="admin-watch-item admin-watch-item--static">
                <span class="admin-watch-label">Matchings au-dessus du seuil</span>
                <span class="admin-watch-value">{{ number_format($stats['matches']['above_threshold'], 0, ',', ' ') }}</span>
                <span class="admin-watch-meta">sur {{ number_format($stats['matches']['total'], 0, ',', ' ') }} matchings</span>
            </div>
            <a href="{{ route('admin.sources.index') }}" class="admin-watch-item">
                <span class="admin-watch-label">Sources de collecte</span>
                <span class="admin-watch-value">{{ $stats['sources']['active'] }} / {{ $stats['sources']['total'] }}</span>
                <span class="admin-watch-meta">sources actives</span>
            </a>
        </section>

        @php
            $revenueTotal = array_sum(array_column($charts['revenue'], 'value'));
            $maxRevenue = max(array_column($charts['revenue'], 'value')) ?: 1;
            $paymentTotal = array_sum(array_column($charts['payment_status'], 'value'));
        @endphp

        <section class="row g-3 mb-4">
            <div class="col-xl-8">
                <div class="app-card h-100">
                    <div class="app-card-header">
                        <div>
                            <h2 class="app-section-title">Revenus des 6 derniers mois</h2>
                            <p class="small text-secondary mb-0">Paiements confirmés, en ariary</p>
                        </div>
                        <span class="small text-secondary admin-figure">{{ number_format($revenueTotal, 0, ',', ' ') }} Ar</span>
                    </div>
                    <div class="app-card-body">
                        <div class="admin-chart" role="img" aria-label="Revenus des six derniers mois">
                            <div class="admin-chart-plot">
                                @foreach ($charts['revenue'] as $point)
                                    <div class="admin-chart-col">
                                        <div class="admin-chart-bar" style="height: {{ max(2, (int) round($point['value'] / $maxRevenue * 100)) }}%;" title="{{ $point['label'] }} : {{ number_format($point['value'], 0, ',', ' ') }} Ar"></div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="admin-chart-labels">
                                @foreach ($charts['revenue'] as $point)
                                    <div class="admin-chart-label">
                                        <span>{{ $point['label'] }}</span>
                                        <span class="admin-chart-figure">{{ number_format($point['value'], 0, ',', ' ') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="app-card h-100">
                    <div class="app-card-header">
                        <h2 class="app-section-title">Statuts des paiements</h2>
                        <a href="{{ route('admin.payments.index') }}" class="small">Voir le suivi</a>
                    </div>
                    <div class="app-card-body">
                        <div class="admin-stack" role="img" aria-label="Répartition des paiements par statut">
                            @forelse ($charts['payment_status'] as $status)
                                @if ($status['value'] > 0)
                                    <div class="admin-stack-segment is-{{ $status['key'] }}" style="width: {{ $status['value'] / $paymentTotal * 100 }}%;" title="{{ $status['label'] }} : {{ $status['value'] }}"></div>
                                @endif
                            @endforeach
                        </div>

                        <ul class="admin-legend">
                            @foreach ($charts['payment_status'] as $status)
                                <li class="admin-legend-item">
                                    <span class="admin-legend-dot is-{{ $status['key'] }}" aria-hidden="true"></span>
                                    <span>{{ $status['label'] }}</span>
                                    <span class="admin-legend-figure">{{ $status['value'] }}</span>
                                </li>
                            @endforeach
                        </ul>

                        @if ($paymentTotal === 0)
                            <p class="small text-secondary mt-3 mb-0">Aucun paiement enregistré.</p>
                        @endif
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
                                    <th class="text-end">Offres actives</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $maxSourceCount = max((int) $stats['offers']['by_source']->max('offers_count'), 1); @endphp
                                @forelse($stats['offers']['by_source'] as $source)
                                    <tr>
                                        <td>
                                            <div class="d-flex justify-content-between gap-3">
                                                <span class="fw-medium">{{ $source->name }}</span>
                                                <span class="small text-secondary">{{ $source->collector_key }}</span>
                                            </div>
                                            <div class="admin-rank-bar" aria-hidden="true">
                                                <span style="width: {{ (int) round($source->offers_count / $maxSourceCount * 100) }}%;"></span>
                                            </div>
                                        </td>
                                        <td class="text-end fw-medium admin-figure">{{ $source->offers_count }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-secondary py-4">Aucune source</td>
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
                                        <td class="text-center admin-figure">{{ $plan->price > 0 ? number_format($plan->price, 0, ',', ' ') . ' Ar' : 'Gratuit' }}</td>
                                        <td class="text-center admin-figure">{{ $plan->sms_quota }}</td>
                                        <td class="text-end fw-medium admin-figure">{{ $plan->subscriptions_count }}</td>
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

        <section class="app-card" aria-label="Activité récente">
            <div class="app-card-header">
                <h2 class="app-section-title">Activité récente</h2>
                <span class="small text-secondary">5 dernières entrées</span>
            </div>
            <div class="row g-0">
                <div class="col-lg-4 admin-activity-column">
                    <div class="d-flex justify-content-between align-items-baseline mb-3">
                        <h3 class="admin-activity-title">Utilisateurs</h3>
                        <a href="{{ route('admin.users.index') }}" class="small">Voir tout</a>
                    </div>
                    <ul class="admin-activity-list">
                        @forelse($recentUsers as $user)
                            <li class="d-flex align-items-center gap-3">
                                <span class="avatar-sm bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-person" aria-hidden="true"></i>
                                </span>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="fw-medium text-truncate">{{ $user->name }}</div>
                                    <div class="small text-secondary text-truncate">{{ $user->email }}</div>
                                </div>
                                <span class="badge">{{ $user->role_label }}</span>
                            </li>
                        @empty
                            <li class="small text-secondary">Aucun utilisateur</li>
                        @endforelse
                    </ul>
                </div>

                <div class="col-lg-4 admin-activity-column">
                    <div class="d-flex justify-content-between align-items-baseline mb-3">
                        <h3 class="admin-activity-title">Offres</h3>
                        <a href="{{ route('admin.offers.index') }}" class="small">Voir tout</a>
                    </div>
                    <ul class="admin-activity-list">
                        @forelse($recentOffers as $offer)
                            <li class="d-flex align-items-center gap-3">
                                <span class="avatar-sm bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-briefcase" aria-hidden="true"></i>
                                </span>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="fw-medium text-truncate">{{ $offer->title }}</div>
                                    <div class="small text-secondary text-truncate">{{ $offer->source->name ?? 'Source inconnue' }}</div>
                                </div>
                                <span class="badge">{{ ucfirst($offer->status) }}</span>
                            </li>
                        @empty
                            <li class="small text-secondary">Aucune offre</li>
                        @endforelse
                    </ul>
                </div>

                <div class="col-lg-4 admin-activity-column">
                    <div class="d-flex justify-content-between align-items-baseline mb-3">
                        <h3 class="admin-activity-title">Paiements</h3>
                        <a href="{{ route('admin.payments.index') }}" class="small">Voir tout</a>
                    </div>
                    <ul class="admin-activity-list">
                        @forelse($recentPayments as $payment)
                            <li class="d-flex align-items-center gap-3">
                                <span class="avatar-sm bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-cash-coin" aria-hidden="true"></i>
                                </span>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="fw-medium text-truncate">{{ $payment->user->name ?? 'Utilisateur' }}</div>
                                    <div class="small text-secondary text-truncate">{{ $payment->created_at->locale('fr')->diffForHumans() }}</div>
                                </div>
                                <span class="fw-medium admin-figure">{{ number_format($payment->amount, 0, ',', ' ') }} Ar</span>
                            </li>
                        @empty
                            <li class="small text-secondary">Aucun paiement</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
