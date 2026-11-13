@include('partials.public-head', ['title' => 'Tarifs — '.config('app.name', 'Job2You')])

    @include('partials.public-navbar')

    <main class="flex-grow-1">
        <section class="public-hero">
            <div class="container">
                <span class="public-eyebrow">Tarifs</span>
                <h1 class="public-hero-title">Une formule par usage, sans engagement.</h1>
                <p class="public-hero-lead">Pour les candidats comme pour les recruteurs. Changez de formule quand votre besoin change.</p>
            </div>
        </section>

        <section class="public-section">
            <div class="container">
                @php
                    $popularPlan = $plans->filter(fn ($plan) => ! $plan->isFree())->sortBy('price')->first();
                    $subscriptionRoute = auth()->check() && auth()->user()->isCandidate()
                        ? route('candidate.subscription')
                        : route('register');
                @endphp

                <div class="row g-4 justify-content-center align-items-stretch">
                    @forelse ($plans as $plan)
                        <div class="col-md-6 col-lg-4">
                            <div class="public-price-card {{ $popularPlan && $popularPlan->id === $plan->id ? 'popular' : '' }}">
                                @if ($popularPlan && $popularPlan->id === $plan->id)
                                    <span class="public-price-badge">Le plus choisi</span>
                                @endif
                                <h3 class="h5 mb-1">{{ ucfirst($plan->name) }}</h3>
                                <p class="text-secondary small mb-3">
                                    {{ $plan->isFree() ? 'Pour découvrir la plateforme' : 'Pour accélérer votre recherche' }}
                                </p>
                                <div class="public-price">
                                    @if ($plan->isFree())
                                        Gratuit
                                    @else
                                        {{ number_format($plan->price, 0, ',', ' ') }}<small>Ar / {{ $plan->duration_days > 0 ? ($plan->duration_days === 30 ? 'mois' : $plan->duration_days.' jours') : 'période' }}</small>
                                    @endif
                                </div>
                                <ul class="public-feature-list">
                                    @foreach ($plan->featureList() as $feature)
                                        <li><i class="bi bi-check-lg" aria-hidden="true"></i>{{ $feature }}</li>
                                    @endforeach
                                </ul>
                                <a href="{{ $subscriptionRoute }}" class="btn {{ $popularPlan && $popularPlan->id === $plan->id ? 'btn-primary' : 'btn-outline-primary' }} w-100">
                                    {{ $plan->isFree() ? 'Commencer gratuitement' : 'Choisir '.ucfirst($plan->name) }}
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center mb-0">Aucune formule n’est disponible pour le moment. Revenez bientôt.</div>
                        </div>
                    @endforelse

                    <div class="col-md-6 col-lg-4">
                        <div class="public-price-card">
                            <h3 class="h5 mb-1">Entreprise</h3>
                            <p class="text-secondary small mb-3">Pour les recruteurs</p>
                            <div class="public-price">450 000<small>Ar / mois</small></div>
                            <ul class="public-feature-list">
                                <li><i class="bi bi-check-lg" aria-hidden="true"></i>Publication d'offres illimitée</li>
                                <li><i class="bi bi-check-lg" aria-hidden="true"></i>Accès à la CVthèque</li>
                                <li><i class="bi bi-check-lg" aria-hidden="true"></i>Matching candidats avancé</li>
                                <li><i class="bi bi-check-lg" aria-hidden="true"></i>Analyses et rapports</li>
                                <li><i class="bi bi-check-lg" aria-hidden="true"></i>Accompagnement dédié</li>
                            </ul>
                            <a href="{{ route('contact') }}" class="btn btn-outline-primary w-100">Contacter l'équipe</a>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap justify-content-center gap-3 mt-4 text-secondary small">
                    <span><i class="bi bi-shield-check me-1" aria-hidden="true"></i>Paiement sécurisé</span>
                    <span><i class="bi bi-arrow-repeat me-1" aria-hidden="true"></i>Sans engagement</span>
                    <span><i class="bi bi-headset me-1" aria-hidden="true"></i>Support réactif</span>
                </div>
            </div>
        </section>

        <section class="public-section public-section-alt">
            <div class="container">
                <div class="public-section-heading">
                    <span class="public-eyebrow">FAQ</span>
                    <h2>Questions fréquentes</h2>
                    <p>Ce qu'il faut savoir avant de choisir votre formule.</p>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="accordion public-faq" id="faqAccordion">
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        Puis-je changer de formule à tout moment ?
                                    </button>
                                </h3>
                                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Oui, vous pouvez passer à une formule supérieure ou inférieure à tout moment. Le changement est effectif immédiatement et nous ajustons la facturation au prorata.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        Y a-t-il un engagement ?
                                    </button>
                                </h3>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Non, tous nos plans sont sans engagement. Vous pouvez résilier votre abonnement à tout moment depuis votre espace personnel.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        Comment fonctionne le matching ?
                                    </button>
                                </h3>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Notre algorithme analyse votre profil (compétences, expérience, préférences) et le compare aux offres disponibles pour calculer un score de compatibilité en temps réel.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="public-section">
            <div class="container">
                <div class="public-cta-band">
                    <h2>Une question sur nos formules ?</h2>
                    <p class="mb-4">Notre équipe vous aide à choisir le plan le plus adapté à votre projet.</p>
                    <a href="{{ route('contact') }}" class="btn btn-light btn-lg">
                        Nous contacter
                    </a>
                </div>
            </div>
        </section>
    </main>

    @include('partials.public-footer')
</body>
</html>
