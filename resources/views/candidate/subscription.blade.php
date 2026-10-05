<x-app-layout>
    <div class="container py-4" style="max-width: 1100px;">
        <h1 class="h3 fw-bold mb-4">Mon abonnement</h1>
        @if ($subscription)
            <div class="bg-white border rounded-3 p-4">
                <h2 class="h5 fw-bold mb-2">{{ $subscription->plan->name ?? 'Formule' }}</h2>
                <p class="mb-1">Statut : <span class="badge text-bg-success">{{ $subscription->status }}</span></p>
                <p class="mb-1">SMS restants : {{ $subscription->sms_remaining }}</p>
                <p class="mb-0">Expire le : {{ $subscription->ends_at?->format('d/m/Y') ?? '—' }}</p>
            </div>
        @else
            <div class="alert alert-info">Vous êtes sur la formule gratuite.</div>
        @endif
    </div>
</x-app-layout>
