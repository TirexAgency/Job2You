<x-app-layout>
    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Administration</p>
                <h1 class="h2 fw-bold mb-1">Historique SMS</h1>
                <p class="text-secondary mb-0">Consultez les alertes envoyées, leurs statuts et la consommation des quotas.</p>
            </div>
            <span class="badge bg-primary-subtle text-primary-emphasis align-self-start align-self-md-center">{{ $logs->total() }} SMS</span>
        </header>

        <section class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <p class="text-secondary small mb-1">Envoyés ce mois</p>
                        <p class="h3 mb-0">{{ number_format($stats['sent_month'], 0, ',', ' ') }}</p>
                        <p class="small text-secondary mb-0">{{ $stats['failed_month'] }} échec(s) ce mois</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <p class="text-secondary small mb-1">En attente</p>
                        <p class="h3 mb-0">{{ $stats['pending'] }}</p>
                        <p class="small text-secondary mb-0">SMS non encore traités</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <p class="text-secondary small mb-1">Quota consommé</p>
                        <p class="h3 mb-0">{{ number_format($stats['quota_consumed'], 0, ',', ' ') }}</p>
                        <p class="small text-secondary mb-0">SMS décomptés d’un quota</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <p class="text-secondary small mb-1">Quota restant</p>
                        <p class="h3 mb-0">{{ number_format($stats['remaining_quota'], 0, ',', ' ') }}</p>
                        <p class="small text-secondary mb-0">{{ $stats['active_subscriptions'] }} abonnement(s) actif(s)</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="app-filter-bar mb-4">
            <form method="GET" action="{{ route('admin.sms.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Rechercher</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Utilisateur, destinataire, message…">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous</option>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="quota_source" class="form-label">Quota</label>
                    <select class="form-select" id="quota_source" name="quota_source">
                        <option value="">Tous</option>
                        <option value="free" {{ request('quota_source') === 'free' ? 'selected' : '' }}>Gratuit</option>
                        <option value="subscription" {{ request('quota_source') === 'subscription' ? 'selected' : '' }}>Abonnement</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary w-100">Filtrer</button>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('admin.sms.index') }}" class="btn btn-outline-secondary w-100">Réinitialiser</a>
                </div>
            </form>
        </section>

        <section class="app-card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Utilisateur</th>
                            <th>Destinataire</th>
                            <th>Statut</th>
                            <th>Message</th>
                            <th>Quota</th>
                            <th>Envoyé le</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            @php
                                $statusClass = match ($log->status) {
                                    'sent' => 'text-bg-success',
                                    'failed' => 'text-bg-danger',
                                    default => 'text-bg-warning',
                                };
                                $quotaLabel = '—';

                                if ($log->subscription) {
                                    $quotaLabel = $log->subscription->smsRemaining().' / '.$log->subscription->smsQuota();
                                } elseif ($log->quota_source === 'free' && $log->user) {
                                    $freeLimit = max((int) $log->user->sms_quota, 0);
                                    $freeRemaining = max($freeLimit - (int) $log->user->sms_sent, 0);
                                    $quotaLabel = $freeRemaining.' / '.$freeLimit;
                                }
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $log->user->name ?? '—' }}</div>
                                    <div class="small text-secondary">{{ $log->user->email ?? '' }}</div>
                                </td>
                                <td class="small">{{ $log->recipient ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $statusClass }}">{{ $statuses[$log->status] ?? ucfirst($log->status) }}</span>
                                    @if ($log->status === 'failed' && $log->error_message)
                                        <div class="small text-danger mt-1">{{ \Illuminate\Support\Str::limit($log->error_message, 80) }}</div>
                                    @endif
                                </td>
                                <td class="small text-secondary">
                                    {{ \Illuminate\Support\Str::limit($log->message ?? '—', 80) }}
                                    @if ($log->provider_status)
                                        <div>{{ $log->provider_status }}</div>
                                    @endif
                                </td>
                                <td class="small">
                                    <div class="fw-semibold admin-figure">{{ $quotaLabel }}</div>
                                    <div class="text-secondary">
                                        {{ $log->quota_source === 'subscription' ? 'Abonnement' : ($log->quota_source === 'free' ? 'Gratuit' : '—') }}
                                    </div>
                                </td>
                                <td class="small text-secondary">{{ $log->sent_at?->format('d/m/Y H:i') ?? $log->created_at?->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="app-empty-state">
                                        <span class="app-empty-icon" aria-hidden="true"><i class="bi bi-chat-dots"></i></span>
                                        <h2 class="h5 fw-bold mb-1">Aucun SMS</h2>
                                        <p class="text-secondary mb-0">Aucune notification ne correspond aux filtres sélectionnés.</p>
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
