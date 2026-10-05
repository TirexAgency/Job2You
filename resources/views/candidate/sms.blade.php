<x-app-layout>
    <div class="container py-4" style="max-width: 1100px;">
        <h1 class="h3 fw-bold mb-4">Mes alertes SMS</h1>
        @forelse ($logs as $log)
            <div class="bg-white border rounded-3 p-4 mb-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-semibold">{{ $log->offer->title ?? 'Notification' }}</div>
                    <div class="small text-secondary">{{ $log->sent_at?->format('d/m/Y H:i') ?? 'En attente' }}</div>
                </div>
                <span class="badge {{ $log->status === 'sent' ? 'text-bg-success' : ($log->status === 'failed' ? 'text-bg-danger' : 'text-bg-warning') }}">{{ ucfirst($log->status) }}</span>
            </div>
        @empty
            <div class="alert alert-info">Aucune alerte SMS envoyée.</div>
        @endforelse
    </div>
</x-app-layout>
