<x-app-layout>
    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Espace candidat</p>
                <h1 class="h2 fw-bold mb-1">Mes alertes SMS</h1>
                <p class="text-secondary mb-0">Soyez prévenu dès qu’une offre fortement compatible est publiée.</p>
            </div>
            <div class="d-flex flex-column flex-md-row align-items-md-center gap-2 align-self-start align-self-md-center">
                <span class="badge bg-primary-subtle text-primary-emphasis px-3 py-2">
                    <i class="bi bi-chat-dots me-1" aria-hidden="true"></i>
                    {{ $smsRemaining }} / {{ $smsQuotaLimit }} SMS
                </span>
                <span class="badge {{ $alertsEnabled ? 'text-bg-success' : 'text-bg-secondary' }} px-3 py-2">
                    <i class="bi {{ $alertsEnabled ? 'bi-bell-fill' : 'bi-bell-slash' }} me-1" aria-hidden="true"></i>
                    Alertes {{ $alertsEnabled ? 'activées' : 'désactivées' }}
                </span>
            </div>
        </header>

        @if ($smsRemaining <= 0)
            <div class="alert alert-warning d-flex align-items-start gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill alert-icon mt-1" aria-hidden="true"></i>
                <div>
                    <strong>Quota SMS épuisé.</strong>
                    Vous avez utilisé vos {{ $smsQuotaLimit }} SMS disponibles. Passez à une formule supérieure pour continuer à recevoir vos alertes.
                    <a href="{{ route('candidate.subscription') }}" class="alert-link">Voir les formules</a>.
                </div>
            </div>
        @endif

        <section class="app-card mb-4">
            <div class="app-card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
                <div class="d-flex align-items-start gap-3">
                    <span class="app-icon {{ $alertsEnabled ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}" aria-hidden="true">
                        <i class="bi {{ $alertsEnabled ? 'bi-bell-fill' : 'bi-bell-slash' }}"></i>
                    </span>
                    <div>
                        <h2 class="app-section-title mb-1">Alertes SMS</h2>
                        <p class="small text-secondary mb-0">Recevez un SMS dès qu’une offre dépasse votre seuil de compatibilité.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('candidate.sms.alerts') }}" class="flex-shrink-0">
                    @csrf
                    <input type="hidden" name="alerts_enabled" value="0">
                    <div class="form-check form-switch mb-0">
                        <input type="checkbox" name="alerts_enabled" value="1" class="form-check-input" id="alerts_enabled" role="switch" @checked($alertsEnabled) onchange="this.form.submit()">
                        <label class="form-check-label" for="alerts_enabled">
                            {{ $alertsEnabled ? 'Désactiver les alertes' : 'Activer les alertes' }}
                        </label>
                    </div>
                    <noscript>
                        <button type="submit" class="btn btn-sm btn-primary mt-2">Enregistrer</button>
                    </noscript>
                </form>
            </div>
        </section>

        <section class="app-card">
            <div class="app-card-header">
                <div>
                    <h2 class="app-section-title">Historique des notifications</h2>
                    <p class="small text-secondary mb-0">Retrouvez les SMS envoyés pour vos correspondances.</p>
                </div>
                <span class="badge bg-primary-subtle text-primary-emphasis">{{ $logs->count() }} notification(s)</span>
            </div>

            <div class="app-card-body p-0">
                @forelse ($logs as $log)
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 px-4 py-3 {{ ! $loop->last ? 'border-bottom' : '' }}">
                        <div class="d-flex align-items-start gap-3">
                            <span class="app-icon bg-primary-subtle text-primary" aria-hidden="true"><i class="bi bi-chat-dots"></i></span>
                            <div>
                                <div class="fw-semibold">{{ $log->offer->title ?? \Illuminate\Support\Str::limit($log->message ?? 'Notification', 80) }}</div>
                                <div class="small text-secondary">
                                    <i class="bi bi-clock me-1" aria-hidden="true"></i>{{ $log->sent_at?->format('d/m/Y H:i') ?? 'En attente d’envoi' }}
                                </div>
                                @if ($log->status === 'failed' && $log->error_message)
                                    <div class="small text-danger mt-1">
                                        <i class="bi bi-exclamation-circle me-1" aria-hidden="true"></i>{{ $log->error_message }}
                                    </div>
                                @endif
                                @if ($log->recipient)
                                    <div class="small text-secondary mt-1">
                                        <i class="bi bi-phone me-1" aria-hidden="true"></i>{{ $log->recipient }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 ms-md-3">
                            <span class="badge {{ $log->status === 'sent' ? 'text-bg-success' : ($log->status === 'failed' ? 'text-bg-danger' : 'text-bg-warning') }}">
                                {{ ucfirst($log->status) }}
                            </span>
                            <form method="POST" action="{{ route('candidate.sms.destroy', $log) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Supprimer" aria-label="Supprimer la notification">
                                    <i class="bi bi-trash" aria-hidden="true"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="app-empty-state">
                        <span class="app-empty-icon" aria-hidden="true"><i class="bi bi-chat-dots"></i></span>
                        <h3 class="h5 fw-bold mb-2">Aucune alerte SMS envoyée</h3>
                        <p class="text-secondary mb-3">Dès qu’une offre correspondra à votre profil, vous recevrez une notification ici.</p>
                        @unless ($alertsEnabled)
                            <p class="small text-warning-emphasis mb-0"><i class="bi bi-exclamation-triangle me-1" aria-hidden="true"></i>Activez les alertes pour être prévenu.</p>
                        @endunless
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
