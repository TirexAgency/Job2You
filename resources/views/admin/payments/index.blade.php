<x-app-layout>
    <div class="container py-4" style="max-width: 1200px;">
        <h1 class="h3 fw-bold mb-4">Paiements & Transactions</h1>
        <div class="bg-white border rounded-3 p-4">
            <table class="table align-middle">
                <thead><tr><th>Utilisateur</th><th>Montant</th><th>Statut</th><th>Date</th></tr></thead>
                <tbody>
                @forelse ($payments as $payment)
                    <tr>
                        <td>{{ $payment->user->name ?? '—' }}</td>
                        <td>{{ number_format($payment->amount ?? 0, 0, ',', ' ') }} Ar</td>
                        <td><span class="badge text-bg-light border">{{ $payment->status ?? '—' }}</span></td>
                        <td>{{ $payment->created_at?->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-secondary">Aucun paiement.</td></tr>
                @endforelse
                </tbody>
            </table>
            {{ $payments->links() }}
        </div>
    </div>
</x-app-layout>
