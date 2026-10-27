<x-app-layout>
    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Administration</p>
                <h1 class="h2 fw-bold mb-1">Gestion des offres d’emploi</h1>
                <p class="text-secondary mb-0">Activez ou désactivez les offres collectées sur la plateforme.</p>
            </div>
            <span class="badge bg-primary-subtle text-primary-emphasis align-self-start align-self-md-center">{{ $offers->total() }} offre(s)</span>
        </header>

        <section class="app-card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Titre</th>
                            <th>Entreprise</th>
                            <th>Localisation</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($offers as $offer)
                            <tr>
                                <td class="fw-semibold">{{ $offer->title }}</td>
                                <td>{{ $offer->company }}</td>
                                <td>{{ $offer->location ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $offer->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $offer->status ?? '—' }}
                                    </span>
                                </td>
                                <td class="small text-secondary">{{ $offer->created_at?->format('d/m/Y') }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.offers.toggle', $offer) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-outline-primary">{{ $offer->status === 'active' ? 'Désactiver' : 'Activer' }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="app-empty-state">
                                        <span class="app-empty-icon" aria-hidden="true"><i class="bi bi-briefcase"></i></span>
                                        <h2 class="h5 fw-bold mb-1">Aucune offre</h2>
                                        <p class="text-secondary mb-0">Les prochaines offres collectées apparaîtront ici.</p>
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
</x-app-layout>
