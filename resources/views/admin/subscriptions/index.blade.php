<x-app-layout>
    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Administration</p>
                <h1 class="h2 fw-bold mb-1">Abonnements</h1>
                <p class="text-secondary mb-0">Suivez les formules actives et les quotas SMS des candidats.</p>
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
                            <th>SMS restants</th>
                            <th>Créé le</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subscriptions as $sub)
                            <tr>
                                <td class="fw-semibold">{{ $sub->user->name ?? '—' }}</td>
                                <td>{{ $sub->plan->name ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $sub->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $sub->status }}
                                    </span>
                                </td>
                                <td>{{ $sub->sms_remaining }}</td>
                                <td class="small text-secondary">{{ $sub->created_at?->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
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
