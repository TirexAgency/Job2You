<x-app-layout>
    <div class="container py-4" style="max-width: 1200px;">
        <h1 class="h3 fw-bold mb-4">Abonnements</h1>
        <div class="bg-white border rounded-3 p-4">
            <table class="table align-middle">
                <thead><tr><th>Utilisateur</th><th>Plan</th><th>Statut</th><th>SMS restants</th><th>Créé le</th></tr></thead>
                <tbody>
                @forelse ($subscriptions as $sub)
                    <tr>
                        <td>{{ $sub->user->name ?? '—' }}</td>
                        <td>{{ $sub->plan->name ?? '—' }}</td>
                        <td><span class="badge text-bg-light border">{{ $sub->status }}</span></td>
                        <td>{{ $sub->sms_remaining }}</td>
                        <td>{{ $sub->created_at?->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-secondary">Aucun abonnement.</td></tr>
                @endforelse
                </tbody>
            </table>
            {{ $subscriptions->links() }}
        </div>
    </div>
</x-app-layout>
