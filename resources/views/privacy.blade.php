@include('partials.public-head', ['title' => 'Politique de confidentialité — '.config('app.name', 'Job2You')])

    @include('partials.public-navbar')

    <main class="flex-grow-1">
        <section class="public-hero pb-4">
            <div class="container">
                <span class="public-eyebrow">Données personnelles</span>
                <h1 class="public-hero-title">Politique de confidentialité</h1>
                <p class="public-hero-lead mb-0">Dernière mise à jour : janvier 2026</p>
            </div>
        </section>

        <section class="public-section pt-4">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="contact-card offer-description">
                            <h2 class="offer-description-heading">1. Collecte des données</h2>
                            <p>Nous collectons les informations que vous nous fournissez lors de l'inscription et de l'utilisation de la plateforme :</p>
                            <ul class="offer-description-list">
                                <li>Informations de contact (nom, email, téléphone)</li>
                                <li>Informations professionnelles (compétences, expérience, CV)</li>
                                <li>Préférences de recherche (localisation, type de contrat)</li>
                                <li>Données d'utilisation (connexions, recherches, interactions)</li>
                            </ul>

                            <h2 class="offer-description-heading">2. Utilisation des données</h2>
                            <p>Vos données sont utilisées pour :</p>
                            <ul class="offer-description-list">
                                <li>Fournir et améliorer nos services de matching</li>
                                <li>Envoyer des alertes SMS et des notifications</li>
                                <li>Personnaliser votre expérience sur la plateforme</li>
                                <li>Assurer la sécurité et la prévention de la fraude</li>
                            </ul>

                            <h2 class="offer-description-heading">3. Partage des données</h2>
                            <p>Nous ne vendons pas vos données personnelles. Vos informations peuvent être partagées avec des entreprises uniquement dans le cadre du processus de recrutement, et uniquement si vous avez manifesté un intérêt pour leurs offres.</p>

                            <h2 class="offer-description-heading">4. Sécurité</h2>
                            <p>Nous mettons en œuvre des mesures de sécurité techniques et organisationnelles pour protéger vos données contre tout accès non autorisé, toute perte ou toute divulgation.</p>

                            <h2 class="offer-description-heading">5. Vos droits</h2>
                            <p>Conformément à la réglementation en vigueur, vous disposez d'un droit d'accès, de rectification, de suppression et d'opposition concernant vos données personnelles. Pour exercer ces droits, écrivez à <a href="mailto:privacy@job2you.com">privacy@job2you.com</a>.</p>

                            <h2 class="offer-description-heading">6. Cookies</h2>
                            <p>Nous utilisons des cookies pour améliorer votre expérience et analyser le trafic. Vous pouvez configurer votre navigateur pour refuser les cookies, mais certaines fonctionnalités pourraient ne plus être accessibles.</p>

                            <h2 class="offer-description-heading">7. Contact</h2>
                            <p class="mb-0">Pour toute question concernant cette politique, écrivez à <a href="mailto:privacy@job2you.com">privacy@job2you.com</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('partials.public-footer')
</body>
</html>
