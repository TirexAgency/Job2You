<x-app-layout>
    <div class="container py-4" style="max-width: 1100px;">
        <h1 class="h3 fw-bold mb-4">Mes recommandations</h1>
        @forelse ($matches as $match)
            <div class="bg-white border rounded-3 p-4 mb-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-semibold">{{ $match->offer->title ?? 'Offre' }}</div>
                    <div class="small text-secondary">{{ $match->offer->company ?? '' }} {{ $match->offer->location ?? '' }}</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge text-bg-success">{{ round($match->score * 100) }}% compatible</span>
                    @if ($match->offer)
                        <a href="{{ route('offers.show', $match->offer) }}" class="btn btn-sm btn-outline-primary">Voir l'offre</a>
                    @endif
                </div>
            </div>
        @empty
            <div class="alert alert-info">Aucune recommandation pour le moment.</div>
        @endforelse
    </div>
</x-app-layout>
