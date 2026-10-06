<x-app-layout>
    <div class="container py-4" style="max-width: 1200px;">
        <h1 class="h3 fw-bold mb-4">Sources de collecte</h1>
        <div class="bg-white border rounded-3 p-4">
            <table class="table align-middle">
                <thead><tr><th>Nom</th><th>Base URL</th><th>Collector key</th><th>Dernière exécution</th><th>Actif</th><th></th></tr></thead>
                <tbody>
                @forelse ($sources as $source)
                    <tr>
                        <td>{{ $source->name }}</td>
                        <td class="small text-secondary">{{ $source->base_url ?? '—' }}</td>
                        <td class="small text-secondary">{{ $source->collector_key }}</td>
                        <td>{{ $source->last_run_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td><span class="badge {{ $source->active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $source->active ? 'Actif' : 'Inactif' }}</span></td>
                        <td>
                            <form method="POST" action="{{ route('admin.sources.toggle', $source) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-outline-primary">{{ $source->active ? 'Désactiver' : 'Activer' }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-secondary">Aucune source.</td></tr>
                @endforelse
                </tbody>
            </table>
            {{ $sources->links() }}
        </div>
    </div>
</x-app-layout>
