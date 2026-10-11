<x-app-layout>
    <div class="container py-4" style="max-width: 800px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold mb-0">Détail de la source</h1>
            <div>
                <a href="{{ route('admin.sources.edit', $source) }}" class="btn btn-outline-primary">Modifier</a>
                <a href="{{ route('admin.sources.index') }}" class="btn btn-outline-secondary">Retour</a>
            </div>
        </div>

        <div class="bg-white border rounded-3 p-4">
            <dl class="row mb-0">
                <dt class="col-sm-3 text-secondary">Nom</dt>
                <dd class="col-sm-9">{{ $source->name }}</dd>

                <dt class="col-sm-3 text-secondary">URL de base</dt>
                <dd class="col-sm-9">
                    @if ($source->base_url)
                        <a href="{{ $source->base_url }}" target="_blank" class="text-truncate d-inline-block" style="max-width: 100%;">{{ $source->base_url }}</a>
                    @else
                        <span class="text-secondary">—</span>
                    @endif
                </dd>

                <dt class="col-sm-3 text-secondary">Clé collecteur</dt>
                <dd class="col-sm-9"><code>{{ $source->collector_key }}</code></dd>

                <dt class="col-sm-3 text-secondary">Statut</dt>
                <dd class="col-sm-9">
                    <span class="badge {{ $source->active ? 'text-bg-success' : 'text-bg-secondary' }}">
                        {{ $source->active ? 'Actif' : 'Inactif' }}
                    </span>
                </dd>

                <dt class="col-sm-3 text-secondary">Dernière exécution</dt>
                <dd class="col-sm-9">{{ $source->last_run_at?->format('d/m/Y H:i') ?? 'Jamais' }}</dd>

                <dt class="col-sm-3 text-secondary">Créée le</dt>
                <dd class="col-sm-9">{{ $source->created_at->format('d/m/Y H:i') }}</dd>

                <dt class="col-sm-3 text-secondary">Modifiée le</dt>
                <dd class="col-sm-9">{{ $source->updated_at->format('d/m/Y H:i') }}</dd>
            </dl>
        </div>

        <div class="mt-4">
            <h2 class="h5 fw-bold mb-3">Offres récentes (10 dernières)</h2>
            @if ($source->offers()->latest()->limit(10)->exists())
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Titre</th><th>Entreprise</th><th>Localisation</th><th>Type</th><th>Statut</th><th>Publiée le</th></tr></thead>
                        <tbody>
                            @foreach ($source->offers()->latest()->limit(10)->get() as $offer)
                                <tr>
                                    <td>{{ $offer->title }}</td>
                                    <td>{{ $offer->company ?? '—' }}</td>
                                    <td>{{ $offer->location ?? '—' }}</td>
                                    <td><span class="badge text-bg-light text-dark">{{ $offer->contract_type ?? '—' }}</span></td>
                                    <td><span class="badge {{ $offer->status === 'active' ? 'text-bg-success' : ($offer->status === 'expired' ? 'text-bg-warning' : 'text-bg-secondary') }}">{{ $offer->status }}</span></td>
                                    <td>{{ $offer->published_at?->format('d/m/Y') ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-secondary">Aucune offre collectée pour cette source.</p>
            @endif
        </div>
    </div>
</x-app-layout>