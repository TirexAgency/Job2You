<x-app-layout>
    <div class="container py-4" style="max-width: 1100px;">
        <header class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <h1 class="h2 fw-bold mb-1">Bonjour {{ auth()->user()->name }} !</h1>
                <p class="text-secondary mb-0">Voici les opportunités correspondant à votre profil aujourd'hui.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('candidate.dashboard', ['view' => 'cv']) }}" class="btn btn-outline-primary">Aperçu du CV</a>
                <a href="{{ route('candidate.profile.edit', ['tab' => 'preferences']) }}" class="btn btn-primary">Ajuster critères</a>
            </div>
        </header>

        @if (! $profile)
            <div class="alert alert-info">Vous n'avez pas encore de profil. <a href="{{ route('candidate.profile.edit') }}">Créez-le maintenant</a>.</div>
        @else
            <section class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="bg-white border rounded-3 p-4 h-100">
                        <p class="small text-secondary mb-1">Marché de l'emploi</p>
                        <p class="h3 fw-bold mb-1">{{ number_format(\App\Models\Offer::count(), 0, ',', ' ') }}</p>
                        <p class="small text-success mb-0">+{{ \App\Models\Offer::where('created_at', '>=', now()->subDay())->count() }} nouvelles offres aujourd'hui</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white border rounded-3 p-4 h-100">
                        <p class="small text-secondary mb-1">Correspondances IA</p>
                        <p class="h3 fw-bold mb-1">{{ \App\Models\JobMatch::where('user_id', auth()->id())->count() }}</p>
                        <p class="small text-success mb-0">≥ 75% de compatibilité</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white border rounded-3 p-4 h-100">
                        <p class="small text-secondary mb-1">Solde notifications</p>
                        <p class="h3 fw-bold mb-1">{{ max(auth()->user()->getSmsRemaining(), 0) }} SMS</p>
                        <p class="small text-success mb-0">Formule {{ auth()->user()->plan === 'free' ? 'Gratuite' : ucfirst(auth()->user()->plan) }}</p>
                    </div>
                </div>
            </section>

            @if ($view === 'cv')
                @include('candidate._cv', ['profile' => $profile])
            @endif

            <section class="bg-white border rounded-3 p-4 mb-4">
                <h2 class="h5 fw-bold mb-3">Mes recommandations</h2>
                @php
                    $recommendations = \App\Models\JobMatch::with('offer')->where('user_id', auth()->id())->orderByDesc('score')->limit(5)->get();
                @endphp
                @forelse ($recommendations as $match)
                    <div class="d-flex justify-content-between align-items-center border rounded-3 p-3 mb-2">
                        <div>
                            <div class="fw-semibold">{{ $match->offer->title ?? 'Offre' }}</div>
                            <div class="small text-secondary">{{ $match->offer->company ?? '' }} · {{ $match->offer->location ?? '' }}</div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge text-bg-success">{{ round($match->score * 100) }}% compatible</span>
                            @if ($match->offer)
                                <a href="{{ route('offers.show', $match->offer) }}" class="btn btn-sm btn-outline-primary">Voir l'offre</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-secondary mb-0">Aucune recommandation pour le moment.</p>
                @endforelse
            </section>
        @endif
    </div>
</x-app-layout>
