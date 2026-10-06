<x-app-layout>
    <div class="container py-4" style="max-width: 1100px;">
        <h1 class="h3 fw-bold mb-4">Mon abonnement</h1>

        @if (session('status') === 'plan-changed')
            <div class="alert alert-success">Votre formule a été mise à jour.</div>
        @endif

        @if ($subscription)
            <div class="bg-white border rounded-3 p-4 mb-4">
                <h2 class="h5 fw-bold mb-2">Formule actuelle : {{ $subscription->plan->name ?? 'Formule' }}</h2>
                <p class="mb-1">Statut : <span class="badge text-bg-success">{{ $subscription->status }}</span></p>
                <p class="mb-1">SMS restants : {{ $subscription->sms_remaining }}</p>
                <p class="mb-0">Expire le : {{ $subscription->ends_at?->format('d/m/Y') ?? '—' }}</p>
            </div>
        @else
            <div class="alert alert-info">Vous êtes sur la formule gratuite.</div>
        @endif

        <h2 class="h5 fw-bold mb-3">Changer de formule</h2>
        <div class="row g-3">
            @foreach ($plans as $plan)
                <div class="col-md-4">
                    <div class="bg-white border rounded-3 p-4 h-100 d-flex flex-column">
                        <h3 class="h6 fw-bold">{{ $plan->name }}</h3>
                        <p class="h4 fw-bold">{{ number_format($plan->price, 0, ',', ' ') }} Ar</p>
                        <p class="small text-secondary">{{ $plan->sms_quota }} SMS / {{ $plan->duration_days }} jours</p>
                        @if ($subscription && $subscription->plan_id === $plan->id)
                            <span class="badge text-bg-success align-self-start mt-auto">Formule actuelle</span>
                        @elseif ($plan->price <= 0)
                            <form method="POST" action="{{ route('candidate.subscription.change') }}" class="mt-auto">
                                @csrf
                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                <button class="btn btn-primary w-100">Activer</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('candidate.checkout', $plan) }}" class="mt-auto">
                                @csrf
                                <button class="btn btn-primary w-100">Souscrire</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
