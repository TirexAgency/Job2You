{{-- Pied de page public Job2You (pages connexion / inscription). --}}
<footer class="job2you-public-footer mt-5">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-12 col-md-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    @include('partials.logo-mark', ['size' => 'sm'])
                </div>
                <p class="small text-secondary mb-0">
                    Plateforme intelligente de matching et recrutement automatisé pour les talents et entreprises de la tech.
                </p>
            </div>

            <div class="col-6 col-md-2">
                <h4 class="h6 fw-medium mb-2">Plateforme</h4>
                <ul class="list-unstyled d-flex flex-column gap-1 mb-0">
                    <li><a href="{{ route('offers.index') }}" class="small">Offres d'emploi</a></li>
                    <li><a href="{{ route('pricing') }}" class="small">Formules &amp; Tarifs</a></li>
                    <li><a href="{{ route('how-it-works') }}" class="small">Fonctionnement</a></li>
                </ul>
            </div>

            <div class="col-6 col-md-2">
                <h4 class="h6 fw-medium mb-2">Assistance &amp; Légal</h4>
                <ul class="list-unstyled d-flex flex-column gap-1 mb-0">
                    <li><a href="{{ route('contact') }}" class="small">Contact</a></li>
                    <li><a href="{{ route('terms') }}" class="small">Conditions</a></li>
                    <li><a href="{{ route('privacy') }}" class="small">Confidentialité</a></li>
                </ul>
            </div>

            <div class="col-12 col-md-4">
                <h4 class="h6 fw-medium mb-2">Statut</h4>
                <div class="d-flex align-items-center gap-1 text-success small fw-semibold">
                    <i class="bi bi-check-circle"></i>
                    Système opérationnel
                </div>
                <p class="small text-secondary mt-1 mb-0">Alertes SMS &amp; Matching instantané actifs</p>
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-3 border-top">
            <p class="small text-secondary mb-0 fw-semibold">&copy; {{ date('Y') }} Job2You. Tous droits réservés.</p>
            <p class="small text-secondary mb-0 fw-semibold mt-2 mt-md-0">Conçu pour le recrutement tech à haute précision.</p>
        </div>
    </div>
</footer>
