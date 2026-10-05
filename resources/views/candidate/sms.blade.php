<x-app-layout>
    <div class="container py-4" style="max-width: 1100px;">
        <h1 class="h3 fw-bold mb-4">Mes alertes SMS</h1>

        @if (session('status') === 'alerts-updated')
            <div class="alert alert-success">Vos alertes ont été mises à jour.</div>
        @elseif (session('status') === 'sms-deleted')
            <div class="alert alert-success">Notification supprimée.</div>
        @endif

        <div class="bg-white border rounded-3 p-4 mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h6 fw-bold mb-1">Alertes SMS</h2>
                <p class="small text-secondary mb-0">Recevez un SMS dès qu'une offre fortement compatible apparaît.</p>
            </div>
            <form method="POST" action="{{ route('candidate.sms.alerts') }}">
                @csrf
                <input type="hidden" name="alerts_enabled" value="{{ $alertsEnabled ? 0 : 1 }}">
                <button class="btn {{ $alertsEnabled ? 'btn-outline-danger' : 'btn-primary' }}">
                    {{ $alertsEnabled ? 'Désactiver les alertes' : 'Activer les alertes' }}
                </button>
            </form>
        </div>

        @forelse ($logs as $log)
            <div class="bg-white border rounded-3 p-4 mb-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-semibold">{{ $log->offer->title ?? 'Notification' }}</div>
                    <div class="small text-secondary">{{ $log->sent_at?->format('d/m/Y H:i') ?? 'En attente' }}</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge {{ $log->status === 'sent' ? 'text-bg-success' : ($log->status === 'failed' ? 'text-bg-danger' : 'text-bg-warning') }}">{{ ucfirst($log->status) }}</span>
                    <form method="POST" action="{{ route('candidate.sms.destroy', $log) }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="alert alert-info">Aucune alerte SMS envoyée.</div>
        @endforelse
    </div>
</x-app-layout>
