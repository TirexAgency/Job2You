<!-- Partial: Carte d'offre réutilisable -->
<div class="col-md-6 col-xl-4">
    <article class="public-offer-card">
        <div class="d-flex align-items-start gap-3">
            <x-company-avatar :name="$offer->company" />

            <div class="min-width-0 flex-grow-1">
                <h3 class="public-offer-title">{{ $offer->title }}</h3>
                <p class="public-offer-company">{{ $offer->company ?: 'Entreprise non précisée' }}</p>
            </div>

            @if($offer->contract_type)
                <span class="public-offer-type">{{ $offer->contract_type }}</span>
            @endif
        </div>

        <div class="public-offer-meta">
            <span>
                <i class="bi bi-geo-alt" aria-hidden="true"></i>
                {{ $offer->location ?: 'Localisation non précisée' }}
            </span>
            <span>
                <i class="bi bi-clock" aria-hidden="true"></i>
                {{ $offer->published_at?->locale('fr')->diffForHumans() ?? 'Récemment' }}
            </span>
        </div>

        @if($offer->offerSkills->count() > 0)
            <div class="d-flex flex-wrap gap-2 mb-3">
                @foreach($offer->offerSkills->take(3) as $offerSkill)
                    <span class="public-skill">{{ $offerSkill->skill->name }}</span>
                @endforeach
                @if($offer->offerSkills->count() > 3)
                    <span class="public-skill">+{{ $offer->offerSkills->count() - 3 }}</span>
                @endif
            </div>
        @endif

        <div class="public-offer-footer">
            <small>
                <i class="bi bi-briefcase me-1" aria-hidden="true"></i>
                {{ $offer->contract_type ?: 'Offre d\'emploi' }}
            </small>
            <a href="{{ route('offers.show', $offer) }}" class="public-offer-link">
                Voir l'offre
                <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </a>
        </div>
    </article>
</div>
