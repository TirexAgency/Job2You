<x-app-layout>
    <div class="container py-4" style="max-width: 700px;">
        <h1 class="h3 fw-bold mb-4">Paiement — {{ $payment->plan->name }}</h1>
        <div class="bg-white border rounded-3 p-4">
            <p>Montant : <strong>{{ number_format($payment->amount, 0, ',', ' ') }} Ar</strong></p>
            <p>Référence : <code>{{ $payment->provider_reference }}</code></p>
            <p class="small text-secondary">Mode sandbox FiveOnePay — en production, le paiement serait confirmé côté serveur via le callback.</p>
            <div class="d-flex gap-2">
                <form method="POST" action="{{ route('candidate.payment.sandbox.confirm', [$payment, 'success']) }}">
                    @csrf
                    <button class="btn btn-success">Simuler paiement réussi</button>
                </form>
                <form method="POST" action="{{ route('candidate.payment.sandbox.confirm', [$payment, 'failed']) }}">
                    @csrf
                    <button class="btn btn-outline-danger">Simuler échec</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
