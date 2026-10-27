<x-app-layout>
    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Administration</p>
                <h1 class="h2 fw-bold mb-1">Paiements & transactions</h1>
                <p class="text-secondary mb-0">Suivez les paiements effectués sur la plateforme.</p>
            </div>
            <span class="badge bg-primary-subtle text-primary-emphasis align-self-start align-self-md-center">{{ $payments->total() }} paiement(s)</span>
        </header>

        <section class="app-card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Utilisateur</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr>
                                <td class="fw-semibold">{{ $payment->user->name ?? '—' }}</td>
                                <td>{{ number_format($payment->amount ?? 0, 0, ',', ' ') }} Ar</td>
                                <td>
                                    <span class="badge {{ $payment->status === 'paid' ? 'text-bg-success' : ($payment->status === 'failed' ? 'text-bg-danger' : 'text-bg-warning') }}">
                                        {{ $payment->status ?? '—' }}
                                    </span>
                                </td>
                                <td class="small text-secondary">{{ $payment->created_at?->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="app-empty-state">
                                        <span class="app-empty-icon" aria-hidden="true"><i class="bi bi-cash-coin"></i></span>
                                        <h2 class="h5 fw-bold mb-1">Aucun paiement</h2>
                                        <p class="text-secondary mb-0">Les transactions apparaîtront ici.</p>
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
</x-app-layout>
