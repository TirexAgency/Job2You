<x-app-layout>
    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Espace candidat</p>
                <h1 class="h2 fw-bold mb-1">Paiement — {{ $payment->plan->name }}</h1>
                <p class="text-secondary mb-0">Simulation de paiement en environnement de test.</p>
            </div>
        </header>

        <section class="app-card" style="max-width: 44rem;">
            <div class="app-card-body">
                <div class="d-flex align-items-start gap-3 mb-4">
                    <span class="app-icon bg-primary-subtle text-primary" aria-hidden="true"><i class="bi bi-credit-card-fill"></i></span>
                    <div>
                        <div class="small text-secondary">Montant à régler</div>
                        <div class="h3 fw-bold mb-0">{{ number_format($payment->amount, 0, ',', ' ') }} Ar</div>
                    </div>
                </div>

                <dl class="row mb-4">
                    <dt class="col-sm-4 text-secondary fw-normal">Référence</dt>
                    <dd class="col-sm-8"><code>{{ $payment->payload_reference }}</code></dd>
                    <dt class="col-sm-4 text-secondary fw-normal">Formule</dt>
                    <dd class="col-sm-8 fw-semibold">{{ $payment->plan?->name }}</dd>
                </dl>

                <div class="alert alert-info d-flex align-items-start gap-2" role="status">
                    <i class="bi bi-info-circle-fill alert-icon mt-1" aria-hidden="true"></i>
                    <div>Mode simulation local — aucun argent n’est débité. En production, la confirmation vient du webhook signé FiveOne Pay.</div>
                </div>

                @if ($payment->error_message)
                    <div class="alert alert-danger" role="alert">{{ $payment->error_message }}</div>
                @endif

                <div class="d-flex flex-column flex-sm-row gap-2">
                    <form method="POST" action="{{ route('candidate.payment.sandbox.confirm', [$payment, 'success']) }}">
                        @csrf
                        <button class="btn btn-success w-100">
                            <i class="bi bi-check-lg me-1" aria-hidden="true"></i>Simuler un paiement réussi
                        </button>
                    </form>
                    <form method="POST" action="{{ route('candidate.payment.sandbox.confirm', [$payment, 'failed']) }}">
                        @csrf
                        <button class="btn btn-outline-danger w-100">
                            <i class="bi bi-x-lg me-1" aria-hidden="true"></i>Simuler un échec
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
