<x-app-layout>
    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Administration</p>
                <h1 class="h2 fw-bold mb-1">Paiements & transactions</h1>
                <p class="text-secondary mb-0">Suivez les paiements, filtrez par statut et remboursez les transactions confirmées.</p>
            </div>
            <span class="badge bg-primary-subtle text-primary-emphasis align-self-start align-self-md-center">{{ $payments->total() }} paiement(s)</span>
        </header>

        <section class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <p class="text-secondary small mb-1">Montant encaissé</p>
                        <p class="h3 mb-0">{{ number_format($stats['paid_total'], 0, ',', ' ') }} Ar</p>
                        <p class="small text-secondary mb-0">{{ $stats['paid'] }} paiement(s) payé(s)</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <p class="text-secondary small mb-1">En attente</p>
                        <p class="h3 mb-0">{{ $stats['pending'] }}</p>
                        <p class="small text-secondary mb-0">transactions non confirmées</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <p class="text-secondary small mb-1">Échecs</p>
                        <p class="h3 mb-0">{{ $stats['failed'] }}</p>
                        <p class="small text-secondary mb-0">transactions refusées ou expirées</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <p class="text-secondary small mb-1">Remboursés</p>
                        <p class="h3 mb-0">{{ $stats['refunded'] }}</p>
                        <p class="small text-secondary mb-0">transactions remboursées</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="app-filter-bar mb-4">
            <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Rechercher</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Utilisateur ou référence">
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
                    <label for="from" class="form-label">Du</label>
                    <input type="date" class="form-control" id="from" name="from" value="{{ request('from') }}">
                </div>
                <div class="col-md-2">
                    <label for="to" class="form-label">Au</label>
                    <input type="date" class="form-control" id="to" name="to" value="{{ request('to') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1">Filtrer</button>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">↺</a>
                </div>
            </form>
        </section>

        <section class="app-card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Utilisateur</th>
                            <th>Formule</th>
                            <th class="text-end">Montant</th>
                            <th>Référence</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            @php
                                $statusClass = match ($payment->status) {
                                    'paid' => 'text-bg-success',
                                    'failed' => 'text-bg-danger',
                                    'refunded' => 'text-bg-secondary',
                                    default => 'text-bg-warning',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $payment->user->name ?? '—' }}</div>
                                    <div class="small text-secondary">{{ $payment->user->email ?? '' }}</div>
                                </td>
                                <td>{{ ucfirst($payment->plan->name ?? '—') }}</td>
                                <td class="text-end fw-semibold admin-figure">{{ number_format((float) $payment->amount, 0, ',', ' ') }} Ar</td>
                                <td class="small">
                                    <code>{{ $payment->payload_reference ?: $payment->provider_reference }}</code>
                                    @if ($payment->operator)
                                        <div class="text-secondary">{{ $payment->operator }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $statusClass }}">{{ $statuses[$payment->status] ?? $payment->status }}</span>
                                    @if ($payment->error_message && $payment->status === 'failed')
                                        <div class="small text-danger mt-1">{{ \Illuminate\Support\Str::limit($payment->error_message, 70) }}</div>
                                    @endif
                                </td>
                                <td class="small text-secondary">
                                    <div>{{ $payment->created_at?->format('d/m/Y H:i') }}</div>
                                    @if ($payment->paid_at)
                                        <div>Payé : {{ $payment->paid_at->format('d/m/Y H:i') }}</div>
                                    @endif
                                    @if ($payment->refunded_at)
                                        <div>Remboursé : {{ $payment->refunded_at->format('d/m/Y H:i') }}</div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($payment->isPaid())
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#refundPaymentModal{{ $payment->id }}">
                                            <i class="bi bi-arrow-counterclockwise me-1" aria-hidden="true"></i>Rembourser
                                        </button>
                                    @else
                                        <span class="small text-secondary">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="app-empty-state">
                                        <span class="app-empty-icon" aria-hidden="true"><i class="bi bi-cash-coin"></i></span>
                                        <h2 class="h5 fw-bold mb-1">Aucun paiement</h2>
                                        <p class="text-secondary mb-0">Aucune transaction ne correspond aux filtres sélectionnés.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        @if ($payments->hasPages())
            <div class="d-flex justify-content-center mt-4">{{ $payments->links() }}</div>
        @endif
    </div>

    @foreach ($payments as $payment)
        @continue(! $payment->isPaid())
        <x-confirm-modal
            id="refundPaymentModal{{ $payment->id }}"
            title="Rembourser le paiement"
            :action="route('admin.payments.refund', $payment)"
            method="PATCH"
            confirm-label="Rembourser"
            :message="'Le paiement de ' . number_format((float) $payment->amount, 0, ',', ' ') . ' Ar sera marqué comme remboursé. L’abonnement de l’utilisateur n’est pas modifié.'"
        />
    @endforeach
</x-app-layout>
