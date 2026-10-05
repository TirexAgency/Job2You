<x-app-layout>
    <div class="container py-4" style="max-width: 1200px;">
        <h1 class="h3 fw-bold mb-4">Historique SMS</h1>
        <div class="bg-white border rounded-3 p-4">
            <table class="table align-middle">
                <thead><tr><th>Utilisateur</th><th>Statut</th><th>Référence</th><th>Envoyé le</th></tr></thead>
                <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td>{{ $log->user->name ?? '—' }}</td>
                        <td><span class="badge {{ $log->status === 'sent' ? 'text-bg-success' : ($log->status === 'failed' ? 'text-bg-danger' : 'text-bg-warning') }}">{{ ucfirst($log->status) }}</span></td>
                        <td class="small text-secondary">{{ $log->provider_reference ?? '—' }}</td>
                        <td>{{ $log->sent_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-secondary">Aucun SMS.</td></tr>
                @endforelse
                </tbody>
            </table>
            {{ $logs->links() }}
        </div>
    </div>
</x-app-layout>
