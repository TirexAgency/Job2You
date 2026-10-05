<x-app-layout>
    <div class="container py-4" style="max-width: 1200px;">
        <header class="mb-4">
            <h1 class="h2 fw-bold mb-1">Tableau de bord Administration</h1>
            <p class="text-secondary mb-0">Vue d'ensemble de la plateforme et de ses performances.</p>
        </header>

        <section class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="bg-white border rounded-3 p-4">
                    <p class="small text-secondary mb-1">Utilisateurs</p>
                    <p class="h3 fw-bold mb-1">{{ number_format($totalUsers, 0, ',', ' ') }}</p>
                    <p class="small text-success mb-0">+{{ $newUsers }} ce mois</p>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="bg-white border rounded-3 p-4">
                    <p class="small text-secondary mb-1">Offres d'emploi</p>
                    <p class="h3 fw-bold mb-1">{{ number_format($totalOffers, 0, ',', ' ') }}</p>
                    <p class="small text-success mb-0">+{{ $newOffers }} cette semaine</p>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="bg-white border rounded-3 p-4">
                    <p class="small text-secondary mb-1">Matchings réalisés</p>
                    <p class="h3 fw-bold mb-1">{{ number_format($totalMatches, 0, ',', ' ') }}</p>
                    <p class="small text-secondary mb-0">total historique</p>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="bg-white border rounded-3 p-4">
                    <p class="small text-secondary mb-1">Abonnements actifs</p>
                    <p class="h3 fw-bold mb-1">{{ $subscriptionsCount }}</p>
                    <p class="small text-secondary mb-0">{{ count($plans) }} formules</p>
                </div>
            </div>
        </section>

        <section class="bg-white border rounded-3 p-4">
            <h2 class="h5 fw-bold mb-3">Accès rapides</h2>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">Utilisateurs</a>
                <a href="{{ route('admin.offers.index') }}" class="btn btn-outline-primary">Offres</a>
                <a href="{{ route('admin.sources.index') }}" class="btn btn-outline-primary">Sources</a>
                <a href="{{ route('admin.plans.index') }}" class="btn btn-outline-primary">Plans</a>
                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-primary">Abonnements</a>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-primary">Paiements</a>
                <a href="{{ route('admin.sms.index') }}" class="btn btn-outline-primary">SMS</a>
                <a href="{{ route('admin.logs.index') }}" class="btn btn-outline-primary">Logs</a>
            </div>
        </section>
    </div>
</x-app-layout>
