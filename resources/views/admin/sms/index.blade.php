<x-app-layout>
    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Administration</p>
                <h1 class="h2 fw-bold mb-1">Historique SMS</h1>
                <p class="text-secondary mb-0">Consultez les alertes envoyées aux candidats.</p>
            </div>
            <span class="badge bg-primary-subtle text-primary-emphasis align-self-start align-self-md-center">{{ $logs->total() }} SMS</span>
        </header>

        <section class="app-card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Utilisateur</th>
                            <th>Statut</th>
                            <th>Message</th>
                            <th>Référence</th>
                            <th>Envoyé le</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $log->user->name ?? '—' }}</div>
                                    <div class="small text-secondary">{{ $log->recipient ?? '—' }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $log->status === 'sent' ? 'text-bg-success' : ($log->status === 'failed' ? 'text-bg-danger' : 'text-bg-warning') }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                    @if ($log->status === 'failed' && $log->error_message)
                                        <div class="small text-danger mt-1">{{ \Illuminate\Support\Str::limit($log->error_message, 80) }}</div>
                                    @endif
                                </td>
                                <td class="small text-secondary">{{ \Illuminate\Support\Str::limit($log->message ?? '—', 80) }}</td>
                                <td class="small text-secondary">
                                    <div>{{ $log->provider_reference ?? '—' }}</div>
                                    @if ($log->provider_status)
                                        <div>{{ $log->provider_status }}</div>
                                    @endif
                                </td>
                                <td class="small text-secondary">{{ $log->sent_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="app-empty-state">
                                        <span class="app-empty-icon" aria-hidden="true"><i class="bi bi-chat-dots"></i></span>
                                        <h2 class="h5 fw-bold mb-1">Aucun SMS</h2>
                                        <p class="text-secondary mb-0">Les notifications envoyées apparaîtront ici.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        @if ($logs->hasPages())
            <div class="d-flex justify-content-center mt-4">{{ $logs->links() }}</div>
        @endif
    </div>
</x-app-layout>
