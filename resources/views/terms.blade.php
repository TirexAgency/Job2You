@include('partials.public-head', ['title' => "Conditions d'utilisation — ".config('app.name', 'Job2You')])

    @include('partials.public-navbar')

    <main class="flex-grow-1">
        <section class="public-hero pb-4">
            <div class="container">
                <span class="public-eyebrow">Mentions légales</span>
                <h1 class="public-hero-title">Conditions d'utilisation</h1>
                <p class="public-hero-lead mb-0">Dernière mise à jour : janvier 2026</p>
            </div>
        </section>

        <section class="public-section pt-4">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="contact-card offer-description">
                            <h2 class="offer-description-heading">1. Acceptation des conditions</h2>
                            <p>En accédant et en utilisant Job2You, vous acceptez d'être lié par les présentes conditions d'utilisation. Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser notre plateforme.</p>

                            <h2 class="offer-description-heading">2. Description du service</h2>
                            <p>Job2You est une plateforme de matching d'offres d'emploi qui connecte les candidats aux entreprises. Nous fournissons un service de mise en relation basé sur un algorithme de compatibilité et des alertes SMS.</p>

                            <h2 class="offer-description-heading">3. Compte utilisateur</h2>
                            <p>Vous êtes responsable de la confidentialité de vos identifiants de connexion. Vous devez nous informer immédiatement de toute utilisation non autorisée de votre compte.</p>

                            <h2 class="offer-description-heading">4. Utilisation acceptable</h2>
                            <p>Vous vous engagez à ne pas utiliser la plateforme à des fins illégales, frauduleuses ou nuisibles. Il est interdit de publier de fausses informations ou de harceler d'autres utilisateurs.</p>

                            <h2 class="offer-description-heading">5. Propriété intellectuelle</h2>
                            <p>L'ensemble du contenu de la plateforme (textes, images, logos, algorithmes) est protégé par le droit d'auteur et est la propriété exclusive de Job2You.</p>

                            <h2 class="offer-description-heading">6. Limitation de responsabilité</h2>
                            <p>Job2You ne garantit pas l'exactitude des offres publiées et ne peut être tenue responsable des pertes ou dommages résultant de l'utilisation de la plateforme.</p>

                            <h2 class="offer-description-heading">7. Modifications</h2>
                            <p>Nous nous réservons le droit de modifier ces conditions à tout moment. Les utilisateurs seront informés des changements importants par email ou notification.</p>

                            <h2 class="offer-description-heading">8. Contact</h2>
                            <p class="mb-0">Pour toute question concernant ces conditions, contactez-nous à l'adresse : <a href="mailto:contact@job2you.com">contact@job2you.com</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('partials.public-footer')
</body>
</html>
