@php
    $scorePercent = isset($score) && $score !== null ? (int) round($score * 100) : null;
@endphp

<article class="card offer-card h-100">
    <div class="card-body d-flex flex-column">
        <div class="d-flex align-items-start gap-3 mb-2">
            <x-company-avatar :name="$offer->company ?? 'Job2You'" size="sm" />
            <div class="flex-grow-1 min-width-0">
                <h2 class="public-offer-title">{{ $offer->title ?? 'Offre sans titre' }}</h2>
                <p class="public-offer-company">{{ $offer->company ?? 'Entreprise' }}</p>
            </div>
        </div>

        <div class="public-offer-meta">
            <span><i class="bi bi-geo-alt" aria-hidden="true"></i>{{ $offer->location ?? 'Localisation non précisée' }}</span>
            @if($offer->contract_type)
                <span><i class="bi bi-file-text" aria-hidden="true"></i>{{ $offer->contract_type }}</span>
            @endif
        </div>

        @if($offer->offerSkills->count() > 0)
            <div class="d-flex flex-wrap gap-1 mb-3">
                @foreach($offer->offerSkills->take(3) as $offerSkill)
                    <span class="public-skill">{{ $offerSkill->skill->name }}</span>
                @endforeach
                @if($offer->offerSkills->count() > 3)
                    <span class="public-skill">+{{ $offer->offerSkills->count() - 3 }}</span>
                @endif
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-auto">
            @if($scorePercent !== null)
                <span class="badge text-bg-success">{{ $scorePercent }} % compatible</span>
            @else
                <span class="text-secondary small">{{ $footnote ?? ($offer->contract_type ?: 'Offre d\'emploi') }}</span>
            @endif
            <a href="{{ route('offers.show', $offer) }}" class="public-offer-link">
                Voir l'offre
                <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</article>
