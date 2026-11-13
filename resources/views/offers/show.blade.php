@include('partials.public-head', ['title' => $offer->title.' — '.config('app.name', 'Job2You')])

    @include('partials.public-navbar')

    <main class="flex-grow-1">
        <section class="public-hero pb-4">
            <div class="container">
                <a href="{{ route('offers.index') }}" class="d-inline-flex align-items-center gap-1 mb-3 text-decoration-none text-secondary">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    <span>Retour aux offres</span>
                </a>

                <div class="row align-items-end g-4">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-start gap-3">
                            <x-company-avatar :name="$offer->company" />

                            <div class="min-width-0">
                                <h1 class="public-hero-title fs-2 mb-2">{{ $offer->title }}</h1>
                                <p class="mb-0 text-secondary">
                                    <i class="bi bi-building me-1" aria-hidden="true"></i>{{ $offer->company }}
                                    @if($offer->source)
                                        <span class="mx-2">·</span>{{ $offer->source->name }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-3">
                            @if($offer->contract_type)
                                <span class="public-offer-type">{{ $offer->contract_type }}</span>
                            @endif
                            <span class="public-skill"><i class="bi bi-geo-alt me-1" aria-hidden="true"></i>{{ $offer->location ?: 'Localisation non précisée' }}</span>
                            <span class="public-skill"><i class="bi bi-calendar me-1" aria-hidden="true"></i>Publié {{ $offer->published_at?->locale('fr')->diffForHumans() }}</span>
                        </div>
                    </div>

                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ $offer->source_url }}" target="_blank" rel="noopener" class="btn btn-primary btn-lg">
                            Postuler
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="public-section pt-4">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="app-card">
                            <div class="app-card-body">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                    <h2 class="h4 mb-0">Description du poste</h2>
                                    @if(filled($offer->description))
                                        <span class="public-skill">
                                            <i class="bi bi-clock me-1" aria-hidden="true"></i>{{ $readingMinutes }} min de lecture
                                        </span>
                                    @endif
                                </div>

                                @if(filled($offer->description))
                                    <div class="offer-description-preview">
                                        <p class="offer-description-excerpt text-secondary">{{ $descriptionExcerpt }}</p>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-3">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#offerDescriptionModal">
                                            Voir la description complète
                                        </button>
                                        <span class="small text-secondary">{{ $descriptionWordCount }} mots</span>
                                    </div>
                                @else
                                    <div class="app-empty-state py-4">
                                        <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
                                        <p class="mb-2">La description détaillée n'est pas disponible pour cette offre.</p>
                                        <a href="{{ $offer->source_url }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">
                                            Consulter l'annonce originale
                                        </a>
                                    </div>
                                @endif

                                @if($offer->offerSkills->count() > 0)
                                    <h3 class="h5 mt-4 mb-3">Compétences requises</h3>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($offer->offerSkills as $offerSkill)
                                            <span class="public-skill {{ $offerSkill->required ? 'border-primary text-primary' : '' }}">
                                                {{ $offerSkill->skill->name }}
                                                @if($offerSkill->required)
                                                    <i class="bi bi-asterisk ms-1" aria-hidden="true"></i>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="d-flex flex-wrap align-items-center gap-3 mt-4 pt-3 border-top">
                                    <a href="{{ $offer->source_url }}" target="_blank" rel="noopener" class="btn btn-primary">
                                        Postuler sur le site de l'entreprise
                                    </a>
                                    <span class="small text-secondary">Vous serez redirigé vers le site source.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="app-card">
                            <div class="app-card-header">
                                <h3 class="app-section-title">Informations</h3>
                            </div>
                            <div class="app-card-body">
                                <div class="d-flex flex-column gap-3">
                                    <div class="d-flex gap-3">
                                        <span class="app-icon"><i class="bi bi-building" aria-hidden="true"></i></span>
                                        <div>
                                            <small class="text-secondary d-block">Entreprise</small>
                                            <span class="fw-medium">{{ $offer->company }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-3">
                                        <span class="app-icon"><i class="bi bi-geo-alt" aria-hidden="true"></i></span>
                                        <div>
                                            <small class="text-secondary d-block">Localisation</small>
                                            <span class="fw-medium">{{ $offer->location ?: 'Non précisée' }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-3">
                                        <span class="app-icon"><i class="bi bi-file-text" aria-hidden="true"></i></span>
                                        <div>
                                            <small class="text-secondary d-block">Type de contrat</small>
                                            <span class="fw-medium">{{ $offer->contract_type ?: 'Non précisé' }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-3">
                                        <span class="app-icon"><i class="bi bi-calendar" aria-hidden="true"></i></span>
                                        <div>
                                            <small class="text-secondary d-block">Publié le</small>
                                            <span class="fw-medium">{{ $offer->published_at?->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                    @if($offer->source)
                                        <div class="d-flex gap-3">
                                            <span class="app-icon"><i class="bi bi-globe" aria-hidden="true"></i></span>
                                            <div>
                                                <small class="text-secondary d-block">Source</small>
                                                <span class="fw-medium">{{ $offer->source->name }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($similarOffers->count() > 0)
                    <div class="mt-5">
                        <div class="public-section-heading">
                            <span class="public-eyebrow">À découvrir aussi</span>
                            <h2>Offres similaires</h2>
                        </div>
                        <div class="row g-3">
                            @foreach($similarOffers as $similarOffer)
                                @include('offers._card', ['offer' => $similarOffer])
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>

        @if(filled($offer->description))
            <div class="modal fade offer-description-modal" id="offerDescriptionModal" tabindex="-1" aria-labelledby="offerDescriptionModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header align-items-start gap-3">
                            <x-company-avatar :name="$offer->company" />

                            <div class="flex-grow-1 min-width-0">
                                <span class="public-eyebrow mb-1">Description du poste</span>
                                <h2 class="modal-title h5 mb-1" id="offerDescriptionModalLabel">{{ $offer->title }}</h2>
                                <div class="d-flex flex-wrap gap-2 small text-secondary">
                                    <span><i class="bi bi-building me-1" aria-hidden="true"></i>{{ $offer->company }}</span>
                                    <span><i class="bi bi-geo-alt me-1" aria-hidden="true"></i>{{ $offer->location ?: 'Non précisée' }}</span>
                                    @if($offer->contract_type)
                                        <span><i class="bi bi-file-text me-1" aria-hidden="true"></i>{{ $offer->contract_type }}</span>
                                    @endif
                                </div>
                            </div>

                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>

                        <div class="modal-body">
                            <div class="offer-description-modal-meta">
                                <span><i class="bi bi-clock" aria-hidden="true"></i>{{ $readingMinutes }} min de lecture</span>
                                <span><i class="bi bi-list-check" aria-hidden="true"></i>{{ $descriptionWordCount }} mots</span>
                                @if($offer->source)
                                    <span><i class="bi bi-globe" aria-hidden="true"></i>{{ $offer->source->name }}</span>
                                @endif
                            </div>

                            @include('offers._description', ['description' => $offer->description])
                        </div>

                        <div class="modal-footer">
                            <span class="small text-secondary me-auto d-none d-sm-inline">Vous serez redirigé vers le site source.</span>
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Fermer</button>
                            <a href="{{ $offer->source_url }}" target="_blank" rel="noopener" class="btn btn-primary">
                                Postuler
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </main>

    @include('partials.public-footer')
</body>
</html>
