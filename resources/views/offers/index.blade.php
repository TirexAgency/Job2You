@include('partials.public-head', ['title' => "Offres d'emploi — ".config('app.name', 'Job2You')])

    @include('partials.public-navbar')

    <main class="flex-grow-1">
        <section class="public-hero">
            <div class="container">
                <span class="public-eyebrow">Le marché tech, en un seul endroit</span>
                <h1 class="public-hero-title">Des offres qui vous correspondent.</h1>
                <p class="public-hero-lead">Filtrez par métier, ville ou contrat et trouvez l'opportunité qui fera avancer votre carrière.</p>

                <div class="public-search-card">
                    <form id="search-form" action="{{ route('offers.index') }}" method="GET">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-4">
                                <div class="public-search-field">
                                    <i class="bi bi-search" aria-hidden="true"></i>
                                    <input type="text" name="search" id="search-input" class="form-control" placeholder="Métier ou compétence" value="{{ request('search') }}" aria-label="Métier ou compétence">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="public-search-field">
                                    <i class="bi bi-geo-alt" aria-hidden="true"></i>
                                    <input type="text" name="location" id="location-input" class="form-control" placeholder="Localisation" value="{{ request('location') }}" aria-label="Localisation">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="public-search-field">
                                    <i class="bi bi-file-text" aria-hidden="true"></i>
                                    <select name="contract_type" id="contract-type-input" class="form-select" aria-label="Type de contrat">
                                        <option value="">Type de contrat</option>
                                        @foreach($contractTypes as $type)
                                            <option value="{{ $type }}" {{ request('contract_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary public-search-submit w-100" id="search-submit">
                                    Rechercher
                                </button>
                            </div>
                        </div>

                        <div class="row g-2 align-items-center mt-3" id="advanced-filters" style="display: none;">
                            <div class="col-md-3">
                                <div class="public-search-field">
                                    <i class="bi bi-calendar" aria-hidden="true"></i>
                                    <input type="date" name="date_from" id="date-from-input" class="form-control" value="{{ request('date_from') }}" aria-label="Date de début">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="public-search-field">
                                    <i class="bi bi-calendar-check" aria-hidden="true"></i>
                                    <input type="date" name="date_to" id="date-to-input" class="form-control" value="{{ request('date_to') }}" aria-label="Date de fin">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="public-search-field">
                                    <i class="bi bi-sort-down" aria-hidden="true"></i>
                                    <select name="sort" id="sort-input" class="form-select" aria-label="Trier les offres">
                                        <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Plus récentes</option>
                                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Plus anciennes</option>
                                        <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Titre A-Z</option>
                                        <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Titre Z-A</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-outline-secondary w-100" id="reset-filters" style="min-height: 2.75rem;">
                                    Réinitialiser
                                </button>
                            </div>
                        </div>

                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-link" id="toggle-advanced-filters">
                                <i class="bi bi-sliders" aria-hidden="true"></i>Filtres avancés
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section class="public-section">
            <div class="container">
                <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
                    <div>
                        <p class="public-eyebrow mb-1" id="total-offers-badge">
                            {{ $offers->total() }} offre{{ $offers->total() > 1 ? 's' : '' }} disponible{{ $offers->total() > 1 ? 's' : '' }}
                        </p>
                        <h2 class="h3 mb-0">Toutes les offres</h2>
                    </div>
                    <span class="text-secondary small">Mise à jour en continu</span>
                </div>

                <div id="offers-container" class="offers-container">
                    @if($offers->count() > 0)
                        <div class="row g-3" id="offers-grid">
                            @foreach($offers as $offer)
                                @include('offers._card', ['offer' => $offer])
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-center mt-4" id="pagination-container">
                            {{ $offers->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="public-empty-state" id="empty-state">
                            <span class="public-empty-icon"><i class="bi bi-inbox" aria-hidden="true"></i></span>
                            <h3 class="h5 mb-2">Aucune offre trouvée</h3>
                            <p class="text-secondary mb-3">Essayez de modifier vos critères de recherche.</p>
                            <a href="{{ route('offers.index') }}" class="btn btn-outline-primary">Réinitialiser les filtres</a>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>

    @include('partials.public-footer')

    <script>
        window.OffersConfig = {
            searchUrl: '{{ route('offers.search') }}',
            indexUrl: '{{ route('offers.index') }}',
        };
    </script>

    @push('scripts')
    <script src="{{ asset('js/offers-search.js') }}"></script>
    @endpush
    @stack('scripts')
</body>
</html>
