@include('partials.public-head', ['title' => 'Comment ça marche — '.config('app.name', 'Job2You')])

    @include('partials.public-navbar')

    <main class="flex-grow-1">
        <section class="public-hero">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <span class="public-eyebrow">Le parcours Job2You</span>
                        <h1 class="public-hero-title">De votre profil à l'opportunité, en trois étapes.</h1>
                        <p class="public-hero-lead">Recevez des offres réellement adaptées à votre parcours, sans perdre de temps à parcourir des listes.</p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                                Créer mon profil
                            </a>
                            <a href="{{ route('offers.index') }}" class="btn btn-outline-primary btn-lg">Voir les offres</a>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="public-hero-preview" aria-label="Déroulé du parcours">
                            <div class="preview-kicker">
                                <span>Votre parcours</span>
                                <span class="text-info">Automatisé</span>
                            </div>

                            <div class="preview-score">
                                <span class="preview-score-ring"><i class="bi bi-check-lg" aria-hidden="true"></i></span>
                                <div>
                                    <strong class="d-block">Profil complété</strong>
                                    <small>Le matching peut travailler.</small>
                                </div>
                            </div>

                            <div class="preview-offer">
                                <span class="preview-offer-icon"><i class="bi bi-person-check" aria-hidden="true"></i></span>
                                <div class="min-width-0">
                                    <strong>1 · Créez votre profil</strong>
                                    <small>Compétences, expérience, préférences</small>
                                </div>
                            </div>
                            <div class="preview-offer">
                                <span class="preview-offer-icon"><i class="bi bi-cpu" aria-hidden="true"></i></span>
                                <div class="min-width-0">
                                    <strong>2 · Le matching analyse</strong>
                                    <small>Score calculé à la publication</small>
                                </div>
                            </div>
                            <div class="preview-offer">
                                <span class="preview-offer-icon"><i class="bi bi-chat-dots" aria-hidden="true"></i></span>
                                <div class="min-width-0">
                                    <strong>3 · Vous recevez vos alertes</strong>
                                    <small>SMS dès qu'une offre correspond</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="public-section">
            <div class="container">
                <div class="public-section-heading">
                    <span class="public-eyebrow">Le déroulé</span>
                    <h2>Comment ça marche</h2>
                    <p>Un parcours simple, de l'inscription à votre prochaine opportunité.</p>
                </div>

                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="public-step-card">
                            <span class="public-step-number">01</span>
                            <div class="public-step-icon"><i class="bi bi-person-plus" aria-hidden="true"></i></div>
                            <h3 class="h5 mb-2">Créez votre profil</h3>
                            <p class="text-secondary mb-0">Inscrivez-vous et complétez vos compétences, votre expérience et vos préférences professionnelles.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="public-step-card">
                            <span class="public-step-number">02</span>
                            <div class="public-step-icon"><i class="bi bi-cpu" aria-hidden="true"></i></div>
                            <h3 class="h5 mb-2">Le matching analyse</h3>
                            <p class="text-secondary mb-0">L'algorithme compare votre profil aux offres disponibles et calcule un score de compatibilité transparent.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="public-step-card">
                            <span class="public-step-number">03</span>
                            <div class="public-step-icon"><i class="bi bi-bell" aria-hidden="true"></i></div>
                            <h3 class="h5 mb-2">Recevez vos alertes</h3>
                            <p class="text-secondary mb-0">Soyez prévenu par SMS dès qu'une offre fortement compatible est publiée.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="public-section public-section-alt">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-5">
                        <div class="public-section-heading mb-0">
                            <span class="public-eyebrow">Pensé pour vous</span>
                            <h2>Votre temps mérite mieux que des candidatures à l'aveugle.</h2>
                            <p>Job2You met la technologie au service de votre projet professionnel.</p>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="public-feature-card">
                                    <div class="public-feature-icon"><i class="bi bi-bullseye" aria-hidden="true"></i></div>
                                    <h3>Score transparent</h3>
                                    <p>Comprenez pourquoi une offre vous correspond grâce à un score détaillé.</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="public-feature-card">
                                    <div class="public-feature-icon"><i class="bi bi-lightning-charge" aria-hidden="true"></i></div>
                                    <h3>Alertes à la publication</h3>
                                    <p>Recevez les meilleures opportunités par SMS, sans actualiser votre boîte mail.</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="public-feature-card">
                                    <div class="public-feature-icon"><i class="bi bi-shield-check" aria-hidden="true"></i></div>
                                    <h3>Données maîtrisées</h3>
                                    <p>Votre profil reste privé et vous contrôlez les informations partagées.</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="public-feature-card">
                                    <div class="public-feature-icon"><i class="bi bi-graph-up-arrow" aria-hidden="true"></i></div>
                                    <h3>Recommandations utiles</h3>
                                    <p>Des offres ciblées plutôt qu'une liste interminable à parcourir.</p>
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
                    <h2>Votre prochain poste commence par un profil.</h2>
                    <p class="mb-4">Rejoignez Job2You et laissez le matching travailler pour vous.</p>
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                        Créer mon compte
                    </a>
                </div>
            </div>
        </section>
    </main>

    @include('partials.public-footer')
</body>
</html>
