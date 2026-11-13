@include('partials.public-head', ['title' => 'Contact — '.config('app.name', 'Job2You')])

    @include('partials.public-navbar')

    <main class="flex-grow-1">
        <section class="public-hero">
            <div class="container">
                <span class="public-eyebrow">Contact</span>
                <h1 class="public-hero-title">Écrivez-nous.</h1>
                <p class="public-hero-lead">Une question, une suggestion ou un partenariat. Nous répondons sous un jour ouvré.</p>
            </div>
        </section>

        <section class="public-section">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="contact-card">
                            <h2 class="h4 mb-3">Envoyer un message</h2>
                            <form>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Nom complet</label>
                                        <input type="text" class="form-control" id="name" placeholder="Votre nom">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" placeholder="votre@email.com">
                                    </div>
                                    <div class="col-12">
                                        <label for="subject" class="form-label">Sujet</label>
                                        <select class="form-select" id="subject">
                                            <option value="">Choisir un sujet</option>
                                            <option value="support">Support technique</option>
                                            <option value="partnership">Partenariat</option>
                                            <option value="feedback">Suggestion</option>
                                            <option value="other">Autre</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="message" class="form-label">Message</label>
                                        <textarea class="form-control" id="message" rows="5" placeholder="Décrivez votre demande"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            Envoyer le message
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="contact-info-card">
                                    <div class="contact-icon">
                                        <i class="bi bi-envelope" aria-hidden="true"></i>
                                    </div>
                                    <h3 class="h6 mb-1">Email</h3>
                                    <p class="text-secondary small mb-0">contact@job2you.com</p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="contact-info-card">
                                    <div class="contact-icon">
                                        <i class="bi bi-telephone" aria-hidden="true"></i>
                                    </div>
                                    <h3 class="h6 mb-1">Téléphone</h3>
                                    <p class="text-secondary small mb-0">+225 07 00 00 00 00</p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="contact-info-card">
                                    <div class="contact-icon">
                                        <i class="bi bi-geo-alt" aria-hidden="true"></i>
                                    </div>
                                    <h3 class="h6 mb-1">Adresse</h3>
                                    <p class="text-secondary small mb-0">Abidjan, Côte d'Ivoire</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('partials.public-footer')
</body>
</html>
