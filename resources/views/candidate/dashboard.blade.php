<x-app-layout>
    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Espace candidat</p>
                <h1>Bonjour {{ auth()->user()->name }}</h1>
                <p class="text-secondary mb-0">Vos correspondances et vos alertes du jour.</p>
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
                                    <article class="card offer-card h-100">
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex align-items-start gap-3 mb-2">
                                                <x-company-avatar :name="$match->offer->company ?? 'Job2You'" size="sm" />
                                                <div class="flex-grow-1 min-width-0">
                                                    <h2 class="public-offer-title">{{ $match->offer->title ?? 'Offre sans titre' }}</h2>
                                                    <p class="public-offer-company">{{ $match->offer->company ?? 'Entreprise' }}</p>
                                                </div>
                                            </div>

                                            <div class="public-offer-meta">
                                                <span><i class="bi bi-geo-alt" aria-hidden="true"></i>{{ $match->offer->location ?? 'Localisation' }}</span>
                                                @if($match->offer->contract_type)
                                                    <span><i class="bi bi-file-text" aria-hidden="true"></i>{{ $match->offer->contract_type }}</span>
                                                @endif
                                            </div>

                                            @if($match->offer->offerSkills->count() > 0)
                                                <div class="d-flex flex-wrap gap-1 mb-3">
                                                    @foreach($match->offer->offerSkills->take(3) as $offerSkill)
                                                        <span class="public-skill">{{ $offerSkill->skill->name }}</span>
                                                    @endforeach
                                                    @if($match->offer->offerSkills->count() > 3)
                                                        <span class="public-skill">+{{ $match->offer->offerSkills->count() - 3 }}</span>
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-auto">
                                                @php($scorePercent = (int) round($match->score * 100))
                                                <span class="badge text-bg-success">{{ $scorePercent }} % compatible</span>
                                                <a href="{{ route('offers.show', $match->offer) }}" class="public-offer-link">
                                                    Voir l'offre
                                                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </article>
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
                                    <article class="card offer-card h-100">
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex align-items-start gap-3 mb-2">
                                                <x-company-avatar :name="$offer->company ?? 'Job2You'" size="sm" />
                                                <div class="flex-grow-1 min-width-0">
                                                    <h2 class="public-offer-title">{{ $offer->title }}</h2>
                                                    <p class="public-offer-company">{{ $offer->company }}</p>
                                                </div>
                                            </div>

                                            <div class="public-offer-meta">
                                                <span><i class="bi bi-geo-alt" aria-hidden="true"></i>{{ $offer->location }}</span>
                                                @if($offer->contract_type)
                                                    <span><i class="bi bi-file-text" aria-hidden="true"></i>{{ $offer->contract_type }}</span>
                                                @endif
                                            </div>

                                            @if($offer->offerSkills->count() > 0)
                                                <div class="d-flex flex-wrap gap-1 mb-3">
                                                    @foreach($offer->offerSkills->take(3) as $offerSkill)
                                                        <span class="public-skill">{{ $offerSkill->skill->name }}</span>
                                                    @endforeach
                                                    @if($offer->offerSkills->count() > 3)
                                                        <span class="public-skill">+{{ $offer->offerSkills->count() - 3 }}</span>
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-auto">
                                                <span class="text-secondary small">{{ $offer->contract_type ?: 'Offre d\'emploi' }}</span>
                                                <a href="{{ route('offers.show', $offer) }}" class="public-offer-link">
                                                    Voir l'offre
                                                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </article>
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
                                    <article class="card offer-card h-100">
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex align-items-start gap-3 mb-2">
                                                <x-company-avatar :name="$offer->company ?? 'Job2You'" size="sm" />
                                                <div class="flex-grow-1 min-width-0">
                                                    <h2 class="public-offer-title">{{ $offer->title }}</h2>
                                                    <p class="public-offer-company">{{ $offer->company }}</p>
                                                </div>
                                            </div>

                                            <div class="public-offer-meta">
                                                <span><i class="bi bi-geo-alt" aria-hidden="true"></i>{{ $offer->location }}</span>
                                                @if($offer->contract_type)
                                                    <span><i class="bi bi-file-text" aria-hidden="true"></i>{{ $offer->contract_type }}</span>
                                                @endif
                                            </div>

                                            @if($offer->offerSkills->count() > 0)
                                                <div class="d-flex flex-wrap gap-1 mb-3">
                                                    @foreach($offer->offerSkills->take(3) as $offerSkill)
                                                        <span class="public-skill">{{ $offerSkill->skill->name }}</span>
                                                    @endforeach
                                                    @if($offer->offerSkills->count() > 3)
                                                        <span class="public-skill">+{{ $offer->offerSkills->count() - 3 }}</span>
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-auto">
                                                <span class="text-secondary small">Suggéré pour votre profil</span>
                                                <a href="{{ route('offers.show', $offer) }}" class="public-offer-link">
                                                    Voir l'offre
                                                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </article>
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
