<x-app-layout>
    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Administration</p>
                <h1 class="h2 fw-bold mb-1">Plans d’abonnement</h1>
                <p class="text-secondary mb-0">Gérez les formules et leurs quotas d’alertes SMS.</p>
            </div>
        </header>

        <section class="app-card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nom</th>
                            <th>Prix</th>
                            <th>Quota SMS</th>
                            <th>Actif</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($plans as $plan)
                            <tr>
                                <td class="fw-semibold">{{ $plan->name }}</td>
                                <td>{{ number_format($plan->price ?? 0, 0, ',', ' ') }} Ar</td>
                                <td>{{ $plan->sms_quota ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $plan->active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $plan->active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.plans.toggle', $plan) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-outline-primary">{{ $plan->active ? 'Désactiver' : 'Activer' }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="app-empty-state">
                                        <span class="app-empty-icon" aria-hidden="true"><i class="bi bi-tags"></i></span>
                                        <h2 class="h5 fw-bold mb-1">Aucun plan</h2>
                                        <p class="text-secondary mb-0">Créez une formule pour commencer.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
