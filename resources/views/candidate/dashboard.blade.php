<x-app-layout>
    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Espace candidat</p>
                <h1>Tableau de bord</h1>
                <p>Vos correspondances et vos alertes du jour.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('candidate.cv') }}" class="btn btn-outline-primary"><i class="bi bi-file-earmark-text" aria-hidden="true"></i>Mon CV</a>
                <a href="{{ route('candidate.profile.edit') }}#t-info" class="btn btn-primary"><i class="bi bi-sliders" aria-hidden="true"></i>Ajuster mes critères</a>
            </div>
        </header>

        @if(! $profile)
            <div class="alert alert-info d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <strong>Votre profil n'est pas encore créé.</strong>
                    Complétez-le pour recevoir des recommandations.
                </div>
                <a href="{{ route('candidate.profile.edit') }}" class="btn btn-primary">
                    Créer mon profil
                </a>
            </div>
        @else
            <section class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <p class="text-secondary small mb-1">Correspondances</p>
                            <p class="h3 mb-1">{{ $stats['matches_total'] }}</p>
                            <p class="small text-secondary mb-0">{{ $stats['matches_above_threshold'] }} au-dessus du seuil</p>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <p class="text-secondary small mb-1">Cette semaine</p>
                            <p class="h3 mb-1">{{ $stats['matches_this_week'] }}</p>
                            <p class="small text-secondary mb-0">nouvelles correspondances</p>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <p class="text-secondary small mb-1">SMS restants</p>
                            <p class="h3 mb-1">{{ $stats['sms_remaining'] }}<span class="text-secondary"> / {{ $stats['sms_quota'] }}</span></p>
                            <p class="small text-secondary mb-0">
                                Formule {{ $stats['plan_display'] }}
                                @if ($stats['subscription']?->ends_at)
                                    · expire le {{ $stats['subscription']->ends_at->format('d/m/Y') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <p class="text-secondary small mb-1">Complétion du profil</p>
                            <p class="h3 mb-1">{{ $profile->completionPercentage() ?? 0 }}%</p>
                            <p class="small text-secondary mb-0">
                                {{ $stats['subscription'] ? 'Abonnement actif' : 'Formule gratuite' }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <ul class="nav nav-pills mb-4" id="dashboardTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="recommendations-tab" data-bs-toggle="pill" data-bs-target="#recommendations" type="button" role="tab">
                        Recommandations
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="all-offers-tab" data-bs-toggle="pill" data-bs-target="#all-offers" type="button" role="tab">
                        Toutes les offres
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="similar-tab" data-bs-toggle="pill" data-bs-target="#similar" type="button" role="tab">
                        Offres similaires
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="dashboardTabsContent">
                <div class="tab-pane fade show active" id="recommendations" role="tabpanel">
                    @if($recommendations->count() > 0)
                        @if($recommendationsAreFallback)
                            <div class="alert alert-info d-flex align-items-start gap-2 mb-3" role="status">
                                <i class="bi bi-info-circle alert-icon" aria-hidden="true"></i>
                                <div>
                                    <strong>Aucune offre n'atteint encore votre seuil d'alerte.</strong>
                                    Voici les meilleures correspondances calculées à partir de votre profil. Complétez vos compétences et vos préférences pour améliorer les résultats.
                                </div>
                            </div>
                        @endif
                        <div class="row g-3">
                            @foreach($recommendations as $match)
                                <div class="col-md-6 col-lg-4">
                                    @include('candidate._match-card', ['offer' => $match->offer, 'score' => $match->score])
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="app-empty-state">
                            <span class="app-empty-icon"><i class="bi bi-bullseye" aria-hidden="true"></i></span>
                            <h2 class="h5 mb-2">Aucune recommandation pour le moment</h2>
                            <p class="text-secondary mb-3">Complétez votre profil et ajustez vos critères pour recevoir des offres personnalisées.</p>
                            <a href="{{ route('candidate.profile.edit', ['tab' => 'preferences']) }}" class="btn btn-primary">
                                Ajuster mes critères
                            </a>
                        </div>
                    @endif
                </div>

                <div class="tab-pane fade" id="all-offers" role="tabpanel">
                    @if($allOffers->count() > 0)
                        <div class="row g-3">
                            @foreach($allOffers as $offer)
                                <div class="col-md-6 col-lg-4">
                                    @include('candidate._match-card', ['offer' => $offer, 'score' => null])
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $allOffers->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="app-empty-state">
                            <span class="app-empty-icon"><i class="bi bi-inbox" aria-hidden="true"></i></span>
                            <h2 class="h5 mb-0">Aucune offre disponible</h2>
                        </div>
                    @endif
                </div>

                <div class="tab-pane fade" id="similar" role="tabpanel">
                    @if($similarOffers->count() > 0)
                        <div class="row g-3">
                            @foreach($similarOffers as $offer)
                                <div class="col-md-6 col-lg-4">
                                    @include('candidate._match-card', ['offer' => $offer, 'score' => null, 'footnote' => 'Suggéré pour votre profil'])
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="app-empty-state">
                            <span class="app-empty-icon"><i class="bi bi-search-heart" aria-hidden="true"></i></span>
                            <h2 class="h5 mb-2">Aucune offre similaire</h2>
                            <p class="text-secondary mb-3">Complétez votre profil (métier souhaité, ville, type de contrat) pour voir des offres similaires.</p>
                            <a href="{{ route('candidate.profile.edit', ['tab' => 'preferences']) }}" class="btn btn-outline-primary">
                                Compléter mon profil
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
