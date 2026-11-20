<x-app-layout>
    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Administration</p>
                <h1 class="h2 fw-bold mb-1">Gestion des offres d’emploi</h1>
                <p class="text-secondary mb-0">Filtrez, modérez et supprimez les offres collectées sur la plateforme.</p>
            </div>
            <span class="badge bg-primary-subtle text-primary-emphasis align-self-start align-self-md-center">{{ $offers->total() }} offre(s)</span>
        </header>

        <section class="row g-3 mb-4">
            <div class="col-sm-4">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <p class="text-secondary small mb-1">Offres actives</p>
                        <p class="h4 mb-0">{{ number_format($stats['active'], 0, ',', ' ') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <p class="text-secondary small mb-1">Offres expirées</p>
                        <p class="h4 mb-0">{{ number_format($stats['expired'], 0, ',', ' ') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <p class="text-secondary small mb-1">Offres archivées</p>
                        <p class="h4 mb-0">{{ number_format($stats['archived'], 0, ',', ' ') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="app-filter-bar mb-4">
            <form method="GET" action="{{ route('admin.offers.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Rechercher</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Titre ou entreprise">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous les statuts</option>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="source_id" class="form-label">Source</label>
                    <select class="form-select" id="source_id" name="source_id">
                        <option value="">Toutes les sources</option>
                        @foreach ($sources as $source)
                            <option value="{{ $source->id }}" {{ (int) request('source_id') === $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1">Filtrer</button>
                    <a href="{{ route('admin.offers.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">↺</a>
                </div>
            </form>
        </section>

        <section class="app-card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Offre</th>
                            <th>Source</th>
                            <th>Localisation</th>
                            <th>Statut</th>
                            <th>Publication</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($offers as $offer)
                            @php
                                $statusClass = match ($offer->status) {
                                    'active' => 'text-bg-success',
                                    'expired' => 'text-bg-warning',
                                    default => 'text-bg-secondary',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-link p-0 text-start fw-semibold text-reset text-decoration-none" data-bs-toggle="modal" data-bs-target="#showOfferModal{{ $offer->id }}">
                                        {{ $offer->title }}
                                    </button>
                                    <div class="small text-secondary">{{ $offer->company }}</div>
                                </td>
                                <td class="small">{{ $offer->source->name ?? '—' }}</td>
                                <td class="small">{{ $offer->location ?? '—' }}</td>
                                <td><span class="badge {{ $statusClass }}">{{ $statuses[$offer->status] ?? $offer->status }}</span></td>
                                <td class="small text-secondary">{{ $offer->published_at?->format('d/m/Y') ?? $offer->created_at?->format('d/m/Y') }}</td>
                                <td>
                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                        <form method="POST" action="{{ route('admin.offers.status', $offer) }}">
                                            @csrf
                                            @method('PATCH')
                                            <select class="form-select form-select-sm" name="status" aria-label="Statut de {{ $offer->title }}" onchange="this.form.submit()">
                                                @foreach ($statuses as $value => $label)
                                                    <option value="{{ $value }}" {{ $offer->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Supprimer" data-bs-toggle="modal" data-bs-target="#deleteOfferModal{{ $offer->id }}">
                                            <i class="bi bi-trash" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="app-empty-state">
                                        <span class="app-empty-icon" aria-hidden="true"><i class="bi bi-briefcase"></i></span>
                                        <h2 class="h5 fw-bold mb-1">Aucune offre</h2>
                                        <p class="text-secondary mb-0">Aucune offre ne correspond aux filtres sélectionnés.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        @if ($offers->hasPages())
            <div class="d-flex justify-content-center mt-4">{{ $offers->links() }}</div>
        @endif
    </div>

    @foreach ($offers as $offer)
        <div class="modal fade" id="showOfferModal{{ $offer->id }}" tabindex="-1" aria-labelledby="showOfferModal{{ $offer->id }}Label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5 fw-bold" id="showOfferModal{{ $offer->id }}Label">{{ $offer->title }}</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-3 text-secondary">Entreprise</dt>
                            <dd class="col-sm-9 fw-semibold">{{ $offer->company }}</dd>

                            <dt class="col-sm-3 text-secondary">Source</dt>
                            <dd class="col-sm-9">{{ $offer->source->name ?? '—' }}</dd>

                            <dt class="col-sm-3 text-secondary">Localisation</dt>
                            <dd class="col-sm-9">{{ $offer->location ?? '—' }}</dd>

                            <dt class="col-sm-3 text-secondary">Contrat</dt>
                            <dd class="col-sm-9">{{ $offer->contract_type ?? '—' }}</dd>

                            <dt class="col-sm-3 text-secondary">Statut</dt>
                            <dd class="col-sm-9">
                                @php
                                    $modalStatusClass = match ($offer->status) {
                                        'active' => 'text-bg-success',
                                        'expired' => 'text-bg-warning',
                                        default => 'text-bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $modalStatusClass }}">{{ $statuses[$offer->status] ?? $offer->status }}</span>
                            </dd>

                            <dt class="col-sm-3 text-secondary">Publiée le</dt>
                            <dd class="col-sm-9">{{ $offer->published_at?->format('d/m/Y H:i') ?? '—' }}</dd>

                            <dt class="col-sm-3 text-secondary">Expire le</dt>
                            <dd class="col-sm-9">{{ $offer->expires_at?->format('d/m/Y') ?? '—' }}</dd>

                            <dt class="col-sm-3 text-secondary">Lien</dt>
                            <dd class="col-sm-9">
                                @if ($offer->source_url)
                                    <a href="{{ $offer->source_url }}" target="_blank" rel="noopener" class="text-truncate d-inline-block" style="max-width: 100%;">{{ $offer->source_url }}</a>
                                @else
                                    —
                                @endif
                            </dd>
                        </dl>

                        @if ($offer->description)
                            <hr>
                            <h3 class="h6 fw-bold mb-2">Description</h3>
                            <div class="small text-secondary" style="white-space: pre-line;">{{ \Illuminate\Support\Str::limit($offer->description, 2000) }}</div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @foreach ($offers as $offer)
        <x-confirm-modal
            id="deleteOfferModal{{ $offer->id }}"
            title="Supprimer l’offre"
            :action="route('admin.offers.destroy', $offer)"
            confirm-label="Supprimer"
            :message="'L’offre « ' . $offer->title . ' » sera définitivement supprimée. Cette action est irréversible.'"
        />
    @endforeach
</x-app-layout>
