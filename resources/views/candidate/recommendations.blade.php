<x-app-layout>
    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Espace candidat</p>
                <h1 class="h2 fw-bold mb-1">Mes recommandations</h1>
                <p class="text-secondary mb-0">Les offres sont classées selon leur compatibilité avec votre profil.</p>
            </div>
            <a href="{{ route('candidate.profile.edit') }}#t-info" class="btn btn-outline-primary align-self-start align-self-md-center">
                <i class="bi bi-sliders me-1" aria-hidden="true"></i>Ajuster mes critères
            </a>
        </header>

        @if (! $profile)
            <div class="app-card">
                <div class="app-empty-state">
                    <span class="app-empty-icon" aria-hidden="true"><i class="bi bi-person-plus"></i></span>
                    <h2 class="h4 fw-bold mb-2">Complétez votre profil</h2>
                    <p class="text-secondary mb-4">Ajoutez vos compétences, votre ville et vos préférences pour recevoir des recommandations personnalisées.</p>
                    <a href="{{ route('candidate.profile.edit') }}" class="btn btn-primary">
                        <i class="bi bi-person-check me-1" aria-hidden="true"></i>Créer mon profil
                    </a>
                </div>
            </div>
        @elseif ($matches->isEmpty())
            <div class="app-card">
                <div class="app-empty-state">
                    <span class="app-empty-icon" aria-hidden="true"><i class="bi bi-stars"></i></span>
                    <h2 class="h4 fw-bold mb-2">Aucune recommandation pour le moment</h2>
                    <p class="text-secondary mb-4">Aucune offre ne correspond encore à vos critères. Complétez votre profil ou ajustez vos préférences de recherche.</p>
                    <a href="{{ route('candidate.profile.edit') }}#t-info" class="btn btn-primary">
                        <i class="bi bi-sliders me-1" aria-hidden="true"></i>Compléter mon profil
                    </a>
                </div>
            </div>
        @else
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <span class="badge bg-primary-subtle text-primary-emphasis">{{ $matches->count() }} offre(s) analysée(s)</span>
                <span class="small text-secondary"><i class="bi bi-info-circle me-1" aria-hidden="true"></i>Le pourcentage indique la proximité avec votre profil.</span>
            </div>

            <div class="row g-3">
                @foreach ($matches as $match)
                    @php
                        $scorePercent = (int) round($match->score * 100);
                        $scoreVariant = $scorePercent >= 70 ? 'success' : ($scorePercent >= 50 ? 'warning' : 'secondary');
                    @endphp

                    <div class="col-md-6">
                        <article class="app-card h-100">
                            <div class="app-card-body">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="app-icon bg-primary-subtle text-primary" aria-hidden="true">
                                        <i class="bi bi-briefcase-fill"></i>
                                    </span>
                                    <div class="min-width-0 flex-grow-1">
                                        <h2 class="h6 fw-bold mb-1">{{ $match->offer->title ?? 'Offre' }}</h2>
                                        <p class="offer-meta mb-1">
                                            <i class="bi bi-building me-1" aria-hidden="true"></i>{{ $match->offer->company ?? 'Entreprise non précisée' }}
                                        </p>
                                        <p class="offer-meta mb-0">
                                            <i class="bi bi-geo-alt me-1" aria-hidden="true"></i>{{ $match->offer->location ?? 'Localisation non précisée' }}
                                        </p>
                                    </div>
                                </div>

                                @if ($match->offer && $match->offer->offerSkills->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-1 mt-3">
                                        @foreach ($match->offer->offerSkills->take(4) as $offerSkill)
                                            <span class="badge bg-primary-subtle text-primary-emphasis">{{ $offerSkill->skill->name }}</span>
                                        @endforeach
                                        @if ($match->offer->offerSkills->count() > 4)
                                            <span class="badge bg-secondary-subtle text-secondary-emphasis">+{{ $match->offer->offerSkills->count() - 4 }}</span>
                                        @endif
                                    </div>
                                @endif

                                <div class="offer-score mt-3">
                                    <div class="progress" role="progressbar" aria-label="Compatibilité" aria-valuenow="{{ $scorePercent }}" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-{{ $scoreVariant }}" style="width: {{ $scorePercent }}%"></div>
                                    </div>
                                    <span class="badge text-bg-{{ $scoreVariant }}">{{ $scorePercent }}%</span>
                                </div>

                                <div class="d-flex justify-content-end mt-3 pt-3 border-top">
                                    @if ($match->offer)
                                        <a href="{{ route('offers.show', $match->offer) }}" class="btn btn-sm btn-outline-primary">
                                            Voir l’offre<i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
