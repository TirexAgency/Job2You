@include('partials.public-head', ['title' => config('app.name', 'Job2You').' — Offres d\'emploi tech à Madagascar'])

    @include('partials.public-navbar')

    <main class="flex-grow-1">
        <section class="public-hero">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <span class="public-eyebrow">Recrutement tech à Madagascar</span>

                        <h1 class="public-hero-title">
                            Les offres qui vous correspondent, avec le score pour le vérifier.
                        </h1>

                        <p class="public-hero-lead">
                            Créez votre profil, recevez un score de compatibilité transparent et une alerte SMS quand une offre vous correspond vraiment.
                        </p>

                        <div class="public-search-card">
                            <form action="{{ route('offers.index') }}" method="GET">
                                <div class="row g-2 align-items-center">
                                    <div class="col-md-5">
                                        <div class="public-search-field">
                                            <i class="bi bi-search" aria-hidden="true"></i>
                                            <input type="text" name="search" class="form-control" placeholder="Métier ou compétence" aria-label="Métier ou compétence">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="public-search-field">
                                            <i class="bi bi-geo-alt" aria-hidden="true"></i>
                                            <input type="text" name="location" class="form-control" placeholder="Ville ou télétravail" aria-label="Localisation">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary public-search-submit w-100">
                                            Rechercher
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="public-quick-links">
                                <span class="small text-secondary me-1">Recherches fréquentes</span>
                                <a href="{{ route('offers.index', ['search' => 'Laravel']) }}">Laravel</a>
                                <a href="{{ route('offers.index', ['search' => 'Vue.js']) }}">Vue.js</a>
                                <a href="{{ route('offers.index', ['search' => 'AWS']) }}">Cloud AWS</a>
                                <a href="{{ route('offers.index', ['location' => 'Télétravail']) }}">Télétravail</a>
                            </div>
                        </div>

                        <div class="public-hero-stats">
                            <div class="public-hero-stat">
                                <strong>{{ $activeOffersCount }}</strong>
                                <span>offres actives</span>
                            </div>
                            <div class="public-hero-stat">
                                <strong>1 min</strong>
                                <span>pour créer son profil</span>
                            </div>
                            <div class="public-hero-stat">
                                <strong>SMS</strong>
                                <span>alertes à la publication</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="public-hero-preview" aria-label="Exemple de correspondance">
                            <div class="preview-kicker">
                                <span>Correspondance</span>
                                <span class="text-info">Mise à jour</span>
                            </div>

                            <div class="preview-score">
                                <span class="preview-score-ring">92%</span>
                                <div>
                                    <strong class="d-block">Développeur full stack</strong>
                                    <small>Antananarivo · 3 offres au-dessus du seuil</small>
                                </div>
                            </div>

                            <div class="preview-offer">
                                <span class="preview-offer-icon"><i class="bi bi-briefcase" aria-hidden="true"></i></span>
                                <div class="min-width-0">
                                    <strong>Développeur Laravel senior</strong>
                                    <small>Fintech · CDI</small>
                                </div>
                                <span class="badge">96%</span>
                            </div>

                            <div class="preview-offer">
                                <span class="preview-offer-icon"><i class="bi bi-code-slash" aria-hidden="true"></i></span>
                                <div class="min-width-0">
                                    <strong>Ingénieur frontend Vue.js</strong>
                                    <small>Éditeur SaaS · Télétravail</small>
                                </div>
                                <span class="badge">88%</span>
                            </div>

                            <div class="preview-offer">
                                <span class="preview-offer-icon"><i class="bi bi-cloud" aria-hidden="true"></i></span>
                                <div class="min-width-0">
                                    <strong>DevOps et cloud AWS</strong>
                                    <small>E-commerce · Hybride</small>
                                </div>
                                <span class="badge">84%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="public-section">
            <div class="container">
                <div class="public-section-heading">
                    <span class="public-eyebrow">Pourquoi Job2You</span>
                    <h2>Moins de candidatures, plus de rencontres justes.</h2>
                    <p>Une plateforme pensée pour faire gagner du temps aux talents comme aux recruteurs.</p>
                </div>

                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="public-feature-card">
                            <div class="public-feature-icon"><i class="bi bi-diagram-3" aria-hidden="true"></i></div>
                            <h3>Offres centralisées</h3>
                            <p>Toutes les opportunités tech réunies au même endroit, avec des informations claires et à jour.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="public-feature-card">
                            <div class="public-feature-icon"><i class="bi bi-bullseye" aria-hidden="true"></i></div>
                            <h3>Score de compatibilité</h3>
                            <p>Un score calculé à partir de vos compétences, de votre expérience et de vos préférences.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="public-feature-card">
                            <div class="public-feature-icon"><i class="bi bi-chat-dots" aria-hidden="true"></i></div>
                            <h3>Alertes SMS</h3>
                            <p>Une alerte dès qu'une offre fortement compatible est publiée. Sans surveiller votre boîte mail.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="public-section public-section-alt">
            <div class="container">
                <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
                    <div class="public-section-heading mb-0">
                        <span class="public-eyebrow">Offres récentes</span>
                        <h2>Les dernières opportunités</h2>
                        <p>Une sélection fraîchement publiée sur la plateforme.</p>
                    </div>
                    <a href="{{ route('offers.index') }}" class="btn btn-outline-primary">
                        Voir toutes les offres
                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                @if($recentOffers->count() > 0)
                    <div class="row g-3">
                        @foreach($recentOffers as $offer)
                            @include('offers._card', ['offer' => $offer])
                        @endforeach
                    </div>
                @else
                    <div class="public-empty-state">
                        <span class="public-empty-icon"><i class="bi bi-inbox" aria-hidden="true"></i></span>
                        <h3 class="h5 mb-2">Aucune offre disponible</h3>
                        <p class="text-secondary mb-0">Revenez bientôt pour découvrir de nouvelles opportunités.</p>
                    </div>
                @endif
            </div>
        </section>

        <section class="public-section">
            <div class="container">
                <div class="public-cta-band">
                    <span class="public-eyebrow">Votre prochain poste</span>
                    <h2>Créez votre profil, le matching fait le reste.</h2>
                    <p class="mb-4">Quelques minutes suffisent pour recevoir des offres classées selon votre parcours.</p>
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                            Créer mon compte
                        </a>
                        <a href="{{ route('offers.index') }}" class="btn btn-outline-light btn-lg">
                            Explorer les offres
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('partials.public-footer')
</body>
</html>
