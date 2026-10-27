<x-app-layout>
    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Espace candidat</p>
                <h1 class="h2 fw-bold mb-1">Mon abonnement</h1>
                <p class="text-secondary mb-0">Gérez votre formule et votre quota d’alertes SMS.</p>
            </div>
            <a href="{{ route('pricing') }}" class="btn btn-outline-primary align-self-start align-self-md-center">
                <i class="bi bi-tags me-1" aria-hidden="true"></i>Comparer les formules
            </a>
        </header>

        @if ($subscription)
            <section class="app-card mb-4">
                <div class="app-card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
                    <div class="d-flex align-items-start gap-3">
                        <span class="app-icon bg-success-subtle text-success" aria-hidden="true"><i class="bi bi-patch-check-fill"></i></span>
                        <div>
                            <div class="small text-secondary mb-1">Formule actuelle</div>
                            <h2 class="h4 fw-bold mb-1">{{ $subscription->plan->name ?? 'Formule' }}</h2>
                            <span class="badge text-bg-success">{{ ucfirst($subscription->status) }}</span>
                        </div>
                    </div>
                    <div class="row g-3 text-center text-md-start">
                        <div class="col-6">
                            <div class="small text-secondary">SMS restants</div>
                            <div class="h5 fw-bold mb-0">{{ $subscription->sms_remaining }}</div>
                        </div>
                        <div class="col-6">
                            <div class="small text-secondary">Expire le</div>
                            <div class="h5 fw-bold mb-0">{{ $subscription->ends_at?->format('d/m/Y') ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </section>
        @else
            <div class="alert alert-info d-flex align-items-start gap-2" role="status">
                <i class="bi bi-info-circle-fill alert-icon mt-1" aria-hidden="true"></i>
                <div>Vous êtes actuellement sur la formule gratuite. Choisissez une formule ci-dessous pour augmenter votre quota d’alertes SMS.</div>
            </div>
        @endif

        <h2 class="h5 fw-bold mb-3">Changer de formule</h2>
        <div class="row g-3">
            @foreach ($plans as $plan)
                @php($isCurrent = $subscription && $subscription->plan_id === $plan->id)
                <div class="col-md-6 col-lg-4">
                    <article class="app-card h-100 {{ $isCurrent ? 'border-primary' : '' }}">
                        <div class="app-card-body h-100 d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                <h3 class="h6 fw-bold mb-0">{{ $plan->name }}</h3>
                                @if ($isCurrent)
                                    <span class="badge text-bg-success">Actuelle</span>
                                @endif
                            </div>
                            <p class="h4 fw-bold mb-1">{{ number_format($plan->price, 0, ',', ' ') }} <span class="fs-6 fw-normal text-secondary">Ar</span></p>
                            <p class="small text-secondary mb-3">{{ $plan->sms_quota }} SMS · {{ $plan->duration_days }} jours</p>

                            <ul class="list-unstyled small text-secondary mb-4 flex-grow-1">
                                <li class="mb-2"><i class="bi bi-check2-circle text-success me-2" aria-hidden="true"></i>{{ $plan->sms_quota }} alertes SMS</li>
                                <li class="mb-2"><i class="bi bi-check2-circle text-success me-2" aria-hidden="true"></i>Recommandations personnalisées</li>
                                <li><i class="bi bi-check2-circle {{ $plan->cv_parsing_enabled ? 'text-success' : 'text-secondary' }} me-2" aria-hidden="true"></i>Analyse de CV {{ $plan->cv_parsing_enabled ? 'incluse' : 'non incluse' }}</li>
                            </ul>

                            @if ($isCurrent)
                                <button class="btn btn-outline-success w-100" type="button" disabled>
                                    <i class="bi bi-check-lg me-1" aria-hidden="true"></i>Formule active
                                </button>
                            @elseif ($plan->price <= 0)
                                <form method="POST" action="{{ route('candidate.subscription.change') }}" class="mt-auto">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                    <button class="btn btn-outline-primary w-100">Activer la formule gratuite</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('candidate.checkout', $plan) }}" class="mt-auto">
                                    @csrf
                                    <button class="btn btn-primary w-100">Souscrire</button>
                                </form>
                            @endif
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
