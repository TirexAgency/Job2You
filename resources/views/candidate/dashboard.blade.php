<x-app-layout>
    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Espace candidat</p>
                <h1 class="h2 fw-bold mb-1">Bonjour {{ auth()->user()->name }} !</h1>
                <p class="text-secondary mb-0">Voici les opportunités qui correspondent le mieux à votre profil aujourd’hui.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('candidate.cv') }}" class="btn btn-outline-primary"><i class="bi bi-file-earmark-text me-1" aria-hidden="true"></i>Mon CV</a>
                <a href="{{ route('candidate.profile.edit') }}#t-info" class="btn btn-primary"><i class="bi bi-sliders me-1" aria-hidden="true"></i>Ajuster mes critères</a>
            </div>
        </header>

        @if(! $profile)
            <div class="alert alert-info d-flex align-items-center justify-content-between">
                <div>
                    <i class="bi bi-person-plus-fill me-2"></i>
                    <strong>Vous n'avez pas encore de profil.</strong> Complétez-le pour recevoir des recommandations personnalisées.
                </div>
                <a href="{{ route('candidate.profile.edit') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Créer mon profil
                </a>
            </div>
        @else
            <!-- Statistiques rapides -->
            <section class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-3">
                                <i class="bi bi-star-fill fs-3"></i>
                            </div>
                            <div>
                                <p class="text-secondary small mb-1">Correspondances</p>
                                <p class="h3 fw-bold mb-0">{{ $stats['matches_total'] }}</p>
                                <p class="small text-success mb-0">{{ $stats['matches_above_threshold'] }} au-dessus du seuil</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="stat-icon bg-success bg-opacity-10 text-success rounded-3">
                                <i class="bi bi-lightning-fill fs-3"></i>
                            </div>
                            <div>
                                <p class="text-secondary small mb-1">Cette semaine</p>
                                <p class="h3 fw-bold mb-0">{{ $stats['matches_this_week'] }}</p>
                                <p class="small text-muted mb-0">Nouvelles recommandations</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-3">
                                <i class="bi bi-chat-dots-fill fs-3"></i>
                            </div>
                            <div>
                                <p class="text-secondary small mb-1">SMS restants</p>
                                <p class="h3 fw-bold mb-0">{{ $stats['sms_remaining'] }} / {{ $stats['sms_quota'] }}</p>
                                <p class="small text-muted mb-0">Formule {{ $stats['plan'] === 'free' ? 'Gratuite' : ucfirst($stats['plan']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="stat-icon bg-info bg-opacity-10 text-info rounded-3">
                                <i class="bi bi-heart-fill fs-3"></i>
                            </div>
                            <div>
                                <p class="text-secondary small mb-1">Complétion profil</p>
                                <p class="h3 fw-bold mb-0">{{ $profile->completionPercentage() ?? 0 }}%</p>
                                <p class="small text-muted mb-0">
                                    @if($stats['subscription'])
                                        <i class="bi bi-check-circle-fill text-success me-1"></i>Abonné
                                    @else
                                        <i class="bi bi-clock text-warning me-1"></i>Gratuit
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Onglets -->
            <ul class="nav nav-pills nav-fill mb-4" id="dashboardTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="recommendations-tab" data-bs-toggle="pill" data-bs-target="#recommendations" type="button" role="tab">
                        <i class="bi bi-stars me-1"></i>Recommandations
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="all-offers-tab" data-bs-toggle="pill" data-bs-target="#all-offers" type="button" role="tab">
                        <i class="bi bi-briefcase me-1"></i>Toutes les offres
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="similar-tab" data-bs-toggle="pill" data-bs-target="#similar" type="button" role="tab">
                        <i class="bi bi-search-heart me-1"></i>Offres similaires
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="dashboardTabsContent">
                <!-- Recommandations (matchings au-dessus du seuil) -->
                <div class="tab-pane fade show active" id="recommendations" role="tabpanel">
                    @if($recommendations->count() > 0)
                        @if($recommendationsAreFallback)
                            <div class="alert alert-info d-flex align-items-start gap-2" role="status">
                                <i class="bi bi-info-circle-fill alert-icon mt-1" aria-hidden="true"></i>
                                <div>
                                    <strong>Aucune offre n’atteint encore votre seuil d’alerte.</strong>
                                    Voici les meilleures correspondances calculées à partir de votre profil. Complétez vos compétences et vos préférences pour améliorer les résultats.
                                </div>
                            </div>
                        @endif
                        <div class="row g-3">
                            @foreach($recommendations as $match)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm offer-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start gap-3 mb-2">
                                                <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                                                    <i class="bi bi-briefcase-fill"></i>
                                                </div>
                                                <div class="flex-grow-1 min-width-0">
                                                    <h5 class="card-title fw-semibold mb-1">{{ $match->offer->title ?? 'Offre sans titre' }}</h5>
                                                    <p class="text-secondary small mb-1">
                                                        <i class="bi bi-building me-1"></i>{{ $match->offer->company ?? 'Entreprise' }}
                                                    </p>
                                                    <p class="text-secondary small mb-0">
                                                        <i class="bi bi-geo-alt me-1"></i>{{ $match->offer->location ?? 'Localisation' }}
                                                    </p>
                                                </div>
                                            </div>

                                            @if($match->offer->offerSkills->count() > 0)
                                                <div class="d-flex flex-wrap gap-1 mb-3">
                                                    @foreach($match->offer->offerSkills->take(3) as $offerSkill)
                                                        <span class="badge bg-primary bg-opacity-10 text-primary">{{ $offerSkill->skill->name }}</span>
                                                    @endforeach
                                                    @if($match->offer->offerSkills->count() > 3)
                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">+{{ $match->offer->offerSkills->count() - 3 }}</span>
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                                @php($scorePercent = (int) round($match->score * 100))
                                                <span class="badge {{ $scorePercent >= 70 ? 'text-bg-success' : ($scorePercent >= 50 ? 'text-bg-warning' : 'text-bg-secondary') }} fw-semibold">
                                                    {{ $scorePercent }}% compatible
                                                </span>
                                                <a href="{{ route('offers.show', $match->offer) }}" class="btn btn-sm btn-outline-primary">
                                                    Voir l'offre
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="avatar-lg bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                                <i class="bi bi-stars fs-1"></i>
                            </div>
                            <h4 class="fw-semibold mb-2">Aucune recommandation pour le moment</h4>
                            <p class="text-secondary mb-4">Complétez votre profil et ajustez vos critères pour recevoir des offres personnalisées.</p>
                            <a href="{{ route('candidate.profile.edit', ['tab' => 'preferences']) }}" class="btn btn-primary">
                                <i class="bi bi-sliders me-1"></i>Ajuster mes critères
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Toutes les offres -->
                <div class="tab-pane fade" id="all-offers" role="tabpanel">
                    @if($allOffers->count() > 0)
                        <div class="row g-3">
                            @foreach($allOffers as $offer)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm offer-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start gap-3 mb-2">
                                                <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                                                    <i class="bi bi-briefcase-fill"></i>
                                                </div>
                                                <div class="flex-grow-1 min-width-0">
                                                    <h5 class="card-title fw-semibold mb-1">{{ $offer->title }}</h5>
                                                    <p class="text-secondary small mb-1">
                                                        <i class="bi bi-building me-1"></i>{{ $offer->company }}
                                                    </p>
                                                    <p class="text-secondary small mb-0">
                                                        <i class="bi bi-geo-alt me-1"></i>{{ $offer->location }}
                                                    </p>
                                                </div>
                                            </div>

                                            @if($offer->offerSkills->count() > 0)
                                                <div class="d-flex flex-wrap gap-1 mb-3">
                                                    @foreach($offer->offerSkills->take(3) as $offerSkill)
                                                        <span class="badge bg-primary bg-opacity-10 text-primary">{{ $offerSkill->skill->name }}</span>
                                                    @endforeach
                                                    @if($offer->offerSkills->count() > 3)
                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">+{{ $offer->offerSkills->count() - 3 }}</span>
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                    {{ $offer->contract_type }}
                                                </span>
                                                <a href="{{ route('offers.show', $offer) }}" class="btn btn-sm btn-outline-primary">
                                                    Voir l'offre
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $allOffers->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-secondary"></i>
                            <h4 class="fw-semibold mt-3">Aucune offre disponible</h4>
                        </div>
                    @endif
                </div>

                <!-- Offres similaires -->
                <div class="tab-pane fade" id="similar" role="tabpanel">
                    @if($similarOffers->count() > 0)
                        <div class="row g-3">
                            @foreach($similarOffers as $offer)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm offer-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start gap-3 mb-2">
                                                <div class="avatar-sm bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                                                    <i class="bi bi-search-heart"></i>
                                                </div>
                                                <div class="flex-grow-1 min-width-0">
                                                    <h5 class="card-title fw-semibold mb-1">{{ $offer->title }}</h5>
                                                    <p class="text-secondary small mb-1">
                                                        <i class="bi bi-building me-1"></i>{{ $offer->company }}
                                                    </p>
                                                    <p class="text-secondary small mb-0">
                                                        <i class="bi bi-geo-alt me-1"></i>{{ $offer->location }}
                                                    </p>
                                                </div>
                                            </div>

                                            @if($offer->offerSkills->count() > 0)
                                                <div class="d-flex flex-wrap gap-1 mb-3">
                                                    @foreach($offer->offerSkills->take(3) as $offerSkill)
                                                        <span class="badge bg-info bg-opacity-10 text-info">{{ $offerSkill->skill->name }}</span>
                                                    @endforeach
                                                    @if($offer->offerSkills->count() > 3)
                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">+{{ $offer->offerSkills->count() - 3 }}</span>
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                                <span class="badge bg-info bg-opacity-10 text-info">Suggéré</span>
                                                <a href="{{ route('offers.show', $offer) }}" class="btn btn-sm btn-outline-info">
                                                    Voir l'offre
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="avatar-lg bg-info bg-opacity-10 text-info rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                                <i class="bi bi-search-heart fs-1"></i>
                            </div>
                            <h4 class="fw-semibold mb-2">Aucune offre similaire</h4>
                            <p class="text-secondary mb-4">Complétez votre profil (métier souhaité, ville, type de contrat) pour voir des offres similaires.</p>
                            <a href="{{ route('candidate.profile.edit', ['tab' => 'preferences']) }}" class="btn btn-outline-primary">
                                <i class="bi bi-sliders me-1"></i>Compléter mon profil
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-app-layout>