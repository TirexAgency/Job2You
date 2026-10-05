<x-app-layout>
    <div class="container py-4" style="max-width: 1200px;">
        <h1 class="h3 fw-bold mb-4">Gestion des offres d'emploi</h1>
        <div class="bg-white border rounded-3 p-4">
            <table class="table align-middle">
                <thead><tr><th>Titre</th><th>Entreprise</th><th>Localisation</th><th>Statut</th><th>Date</th></tr></thead>
                <tbody>
                @forelse ($offers as $offer)
                    <tr>
                        <td>{{ $offer->title }}</td>
                        <td>{{ $offer->company }}</td>
                        <td>{{ $offer->location }}</td>
                        <td><span class="badge text-bg-light border">{{ $offer->status ?? '—' }}</span></td>
                        <td>{{ $offer->created_at?->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-secondary">Aucune offre.</td></tr>
                @endforelse
                </tbody>
            </table>
            {{ $offers->links() }}
        </div>
    </div>
</x-app-layout>
