<footer class="public-footer mt-auto">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                <a href="{{ route('home') }}" class="footer-brand" aria-label="Job2You, accueil">
                    <img src="{{ asset('logo.png') }}" alt="Job2You">
                </a>
                <p class="footer-description">La plateforme intelligente qui connecte les talents aux meilleures opportunités tech à Madagascar et au-delà.</p>
                <div class="mt-3">
                    <span class="footer-status">
                        <i class="bi bi-check-circle-fill" aria-hidden="true"></i>Système opérationnel
                    </span>
                </div>
            </div>

            <div class="col-6 col-lg-2">
                <h4>Plateforme</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('offers.index') }}">Offres d'emploi</a></li>
                    <li><a href="{{ route('pricing') }}">Formules & tarifs</a></li>
                    <li><a href="{{ route('how-it-works') }}">Fonctionnement</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-2">
                <h4>Assistance</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                    <li><a href="{{ route('terms') }}">Conditions</a></li>
                    <li><a href="{{ route('privacy') }}">Confidentialité</a></li>
                </ul>
            </div>

            <div class="col-lg-3">
                <h4>Alertes</h4>
                <p class="mb-2 text-white fw-semibold">Ne manquez plus une opportunité.</p>
                <p class="small mb-3">Matching instantané et notifications SMS pour les offres qui vous correspondent vraiment.</p>
                @guest
                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-bell me-1" aria-hidden="true"></i>Activer mes alertes
                    </a>
                @endguest
            </div>
        </div>

        <div class="footer-bottom">
            <span>&copy; {{ date('Y') }} Job2You. Tous droits réservés.</span>
            <span>Conçu pour un recrutement tech à haute précision.</span>
        </div>
    </div>
</footer>
