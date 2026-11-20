<x-app-layout>
    @php
        $smsUsed = max($smsQuotaLimit - $smsRemaining, 0);
        $usagePercent = $smsQuotaLimit > 0 ? (int) round(min(100, ($smsUsed / $smsQuotaLimit) * 100)) : 0;
    @endphp

    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Espace candidat</p>
                <h1 class="h2 fw-bold mb-1">Mon abonnement</h1>
                <p class="text-secondary mb-0">Gérez votre formule, suivez votre consommation et vos droits d’accès.</p>
            </div>
            <a href="{{ route('pricing') }}" class="btn btn-outline-primary align-self-start align-self-md-center">
                <i class="bi bi-tags me-1" aria-hidden="true"></i>Comparer les formules
            </a>
        </header>

        @if ($pendingPayment)
            <section class="alert alert-warning d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3" role="status">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-hourglass-split fs-4 mt-1" aria-hidden="true"></i>
                    <div>
                        <div class="fw-semibold">Paiement en attente — {{ $pendingPayment->plan?->name }}</div>
                        <div class="small">
                            {{ number_format($pendingPayment->amount, 0, ',', ' ') }} Ar · référence
                            <code>{{ $pendingPayment->payload_reference }}</code>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a href="{{ $pendingPayment->payment_url }}" class="btn btn-warning">
                        <i class="bi bi-credit-card me-1" aria-hidden="true"></i>Reprendre le paiement
                    </a>
                    <a href="{{ route('candidate.payment.status', $pendingPayment) }}" class="btn btn-outline-dark">
                        <i class="bi bi-arrow-clockwise me-1" aria-hidden="true"></i>Vérifier l’état
                    </a>
                </div>
            </section>
        @endif

        <section class="app-card mb-4">
            <div class="app-card-body">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
                    <div class="d-flex align-items-start gap-3">
                        <span class="app-icon bg-success-subtle text-success" aria-hidden="true"><i class="bi bi-patch-check-fill"></i></span>
                        <div>
                            <div class="small text-secondary mb-1">Formule actuelle</div>
                            <h2 class="h4 fw-bold mb-1">{{ $subscription?->plan?->name ?? 'Gratuite' }}</h2>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="badge {{ $subscription?->isActive() ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $subscription?->isActive() ? 'Abonnement actif' : 'Formule gratuite' }}
                                </span>
                                @if ($subscription?->ends_at)
                                    <span class="badge text-bg-light border">
                                        <i class="bi bi-calendar-event me-1" aria-hidden="true"></i>
                                        Expire le {{ $subscription->ends_at->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="badge text-bg-light border">
                                        <i class="bi bi-infinity me-1" aria-hidden="true"></i>Sans expiration
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 text-center text-md-start">
                        <div class="col-6">
                            <div class="small text-secondary">SMS restants</div>
                            <div class="h5 fw-bold mb-0">{{ $smsRemaining }} / {{ $smsQuotaLimit }}</div>
                        </div>
                        <div class="col-6">
                            <div class="small text-secondary">Jours restants</div>
                            <div class="h5 fw-bold mb-0">
                                {{ $subscription?->daysRemaining() ?? '∞' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center small mb-2">
                        <span class="text-secondary">Consommation SMS de la période</span>
                        <span class="fw-semibold">{{ $smsUsed }} utilisé{{ $smsUsed > 1 ? 's' : '' }} · {{ $smsRemaining }} restant{{ $smsRemaining > 1 ? 's' : '' }}</span>
                    </div>
                    <div class="progress" role="progressbar" aria-label="Consommation du quota SMS" aria-valuenow="{{ $usagePercent }}" aria-valuemin="0" aria-valuemax="100" style="height: 10px;">
                        <div class="progress-bar {{ $usagePercent >= 90 ? 'bg-danger' : ($usagePercent >= 70 ? 'bg-warning' : 'bg-success') }}" style="width: {{ $usagePercent }}%"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-4">
            <h2 class="h5 fw-bold mb-3">Vos droits avec cette formule</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="app-card h-100">
                        <div class="app-card-body d-flex align-items-start gap-3">
                            <span class="app-icon bg-primary-subtle text-primary" aria-hidden="true"><i class="bi bi-chat-dots-fill"></i></span>
                            <div>
                                <h3 class="h6 fw-bold mb-1">Alertes SMS</h3>
                                <p class="small text-secondary mb-0">
                                    {{ $smsQuotaLimit > 0 ? 'Accès autorisé avec '.$smsQuotaLimit.' SMS par période.' : 'Aucune alerte SMS incluse dans cette formule.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="app-card h-100">
                        <div class="app-card-body d-flex align-items-start gap-3">
                            <span class="app-icon {{ $subscription?->plan?->cv_parsing_enabled ? 'bg-primary-subtle text-primary' : 'bg-secondary-subtle text-secondary' }}" aria-hidden="true"><i class="bi bi-file-earmark-text-fill"></i></span>
                            <div>
                                <h3 class="h6 fw-bold mb-1">Analyse de CV par IA</h3>
                                <p class="small text-secondary mb-0">
                                    {{ $subscription?->plan?->cv_parsing_enabled ? 'Fonctionnalité incluse dans votre formule.' : 'Non incluse. Passez à une formule supérieure pour y accéder.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <h2 class="h5 fw-bold mb-3">Changer de formule</h2>
        <div class="row g-3">
            @forelse ($plans as $plan)
                @php($isCurrent = $subscription && $subscription->plan_id === $plan->id)
                <div class="col-md-6 col-lg-4">
                    <article class="app-card h-100 {{ $isCurrent ? 'border-primary' : '' }}">
                        <div class="app-card-body h-100 d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                <h3 class="h6 fw-bold mb-0">{{ ucfirst($plan->name) }}</h3>
                                @if ($isCurrent)
                                    <span class="badge text-bg-success">Actuelle</span>
                                @elseif (! $plan->active)
                                    <span class="badge text-bg-secondary">Indisponible</span>
                                @endif
                            </div>
                            <p class="h4 fw-bold mb-1">
                                @if ($plan->isFree())
                                    Gratuit
                                @else
                                    {{ number_format($plan->price, 0, ',', ' ') }} <span class="fs-6 fw-normal text-secondary">Ar</span>
                                @endif
                            </p>
                            <p class="small text-secondary mb-3">
                                {{ $plan->duration_days > 0 ? $plan->duration_days.' jours' : 'Sans engagement' }}
                            </p>

                            <ul class="list-unstyled small text-secondary mb-4 flex-grow-1">
                                @foreach ($plan->featureList() as $feature)
                                    <li class="mb-2"><i class="bi bi-check2-circle text-success me-2" aria-hidden="true"></i>{{ $feature }}</li>
                                @endforeach
                            </ul>

                            @if ($isCurrent)
                                <button class="btn btn-outline-success w-100" type="button" disabled>
                                    <i class="bi bi-check-lg me-1" aria-hidden="true"></i>Formule active
                                </button>
                            @elseif ($plan->isFree())
                                <form method="POST" action="{{ route('candidate.subscription.change') }}" class="mt-auto">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                    <button class="btn btn-outline-primary w-100">Activer la formule gratuite</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('candidate.checkout', $plan) }}" class="mt-auto">
                                    @csrf
                                    <button class="btn btn-primary w-100">
                                        <i class="bi bi-phone me-1" aria-hidden="true"></i>Payer par Mobile Money
                                    </button>
                                </form>
                            @endif
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info mb-0">Aucune formule n’est disponible pour le moment.</div>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
