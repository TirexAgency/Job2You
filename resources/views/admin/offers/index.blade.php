<x-app-layout>
    <div class="container py-4" style="max-width: 1200px;">
        <h1 class="h3 fw-bold mb-4">Gestion des offres d'emploi</h1>
        <div class="bg-white border rounded-3 p-4">
            <table class="table align-middle">
                <thead><tr><th>Titre</th><th>Entreprise</th><th>Localisation</th><th>Statut</th><th>Date</th><th></th></tr></thead>
                <tbody>
                @forelse ($offers as $offer)
                    <tr>
                        <td>{{ $offer->title }}</td>
                        <td>{{ $offer->company }}</td>
                        <td>{{ $offer->location }}</td>
                        <td><span class="badge text-bg-light border">{{ $offer->status ?? '—' }}</span></td>
                        <td>{{ $offer->created_at?->format('d/m/Y') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.offers.toggle', $offer) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-outline-primary">{{ $offer->status === 'active' ? 'Désactiver' : 'Activer' }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-secondary">Aucune offre.</td></tr>
                @endforelse
                </tbody>
            </table>
            {{ $offers->links() }}
        </div>
    </div>
</x-app-layout>
