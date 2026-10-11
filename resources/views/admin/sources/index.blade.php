<x-app-layout>
    <div class="container py-4" style="max-width: 1200px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold mb-0">Sources de collecte</h1>
            <a href="{{ route('admin.sources.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Nouvelle source
            </a>
        </div>

        <div class="bg-white border rounded-3 p-4">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Base URL</th>
                            <th>Collector key</th>
                            <th>Dernière exécution</th>
                            <th>Actif</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($sources as $source)
                        <tr>
                            <td>{{ $source->name }}</td>
                            <td class="small text-secondary">
                                @if ($source->base_url)
                                    <a href="{{ $source->base_url }}" target="_blank" class="text-truncate d-inline-block" style="max-width: 300px;">{{ $source->base_url }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="small text-secondary"><code>{{ $source->collector_key }}</code></td>
                            <td>{{ $source->last_run_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $source->active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $source->active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.sources.show', $source) }}" class="btn btn-outline-secondary" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.sources.edit', $source) }}" class="btn btn-outline-primary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.sources.toggle', $source) }}" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-outline-{{ $source->active ? 'warning' : 'success' }}" title="{{ $source->active ? 'Désactiver' : 'Activer' }}">
                                            <i class="bi bi-{{ $source->active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.sources.destroy', $source) }}" class="d-inline" onsubmit="return confirm('Supprimer cette source ?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-4">Aucune source.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $sources->links() }}
        </div>
    </div>
</x-app-layout>