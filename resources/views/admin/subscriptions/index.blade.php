<x-app-layout>
    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Administration</p>
                <h1 class="h2 fw-bold mb-1">Abonnements</h1>
                <p class="text-secondary mb-0">Suivez les formules, les échéances et la consommation des quotas par candidat.</p>
            </div>
            <span class="badge bg-primary-subtle text-primary-emphasis align-self-start align-self-md-center">{{ $subscriptions->total() }} abonnement(s)</span>
        </header>

        <section class="app-card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Utilisateur</th>
                            <th>Plan</th>
                            <th>Statut</th>
                            <th>Consommation SMS</th>
                            <th>Période</th>
                            <th>Créé le</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subscriptions as $sub)
                            @php
                                $isActive = $sub->isActive();
                                $statusLabel = match (true) {
                                    $isActive => 'Actif',
                                    $sub->status === 'suspended' => 'Suspendu',
                                    $sub->ends_at?->isPast() => 'Expiré',
                                    default => 'Inactif',
                                };
                                $statusClass = match ($statusLabel) {
                                    'Actif' => 'text-bg-success',
                                    'Suspendu' => 'text-bg-warning',
                                    'Expiré' => 'text-bg-danger',
                                    default => 'text-bg-secondary',
                                };
                                $quota = (int) ($sub->plan?->sms_quota ?? 0);
                                $used = $sub->smsUsed();
                                $percentage = $sub->smsUsagePercentage();
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $sub->user->name ?? '—' }}</div>
                                    <div class="small text-secondary">{{ $sub->user->email ?? '' }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $sub->plan?->name ?? '—' }}</div>
                                    <div class="small text-secondary">
                                        @if ($sub->plan?->isFree())
                                            Gratuit
                                        @else
                                            {{ number_format((float) $sub->plan?->price, 0, ',', ' ') }} Ar
                                        @endif
                                    </div>
                                </td>
                                <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                <td style="min-width: 170px;">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="fw-semibold">{{ $used }} / {{ $quota }}</span>
                                        <span class="text-secondary">{{ $percentage }} %</span>
                                    </div>
                                    <div class="progress" role="progressbar" aria-label="Consommation SMS" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100" style="height: 7px;">
                                        <div class="progress-bar {{ $percentage >= 90 ? 'bg-danger' : ($percentage >= 70 ? 'bg-warning' : 'bg-success') }}" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </td>
                                <td class="small text-secondary">
                                    <div>{{ $sub->starts_at?->format('d/m/Y') ?? '—' }}</div>
                                    <div>→ {{ $sub->ends_at?->format('d/m/Y') ?? 'Sans expiration' }}</div>
                                </td>
                                <td class="small text-secondary">{{ $sub->created_at?->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="app-empty-state">
                                        <span class="app-empty-icon" aria-hidden="true"><i class="bi bi-repeat"></i></span>
                                        <h2 class="h5 fw-bold mb-1">Aucun abonnement</h2>
                                        <p class="text-secondary mb-0">Les abonnements souscrits apparaîtront ici.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        @if ($subscriptions->hasPages())
            <div class="d-flex justify-content-center mt-4">{{ $subscriptions->links() }}</div>
        @endif
    </div>
</x-app-layout>
