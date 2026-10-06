<x-app-layout>
    <div class="container py-4" style="max-width: 1200px;">
        <h1 class="h3 fw-bold mb-4">Plans d'abonnement</h1>
        <div class="bg-white border rounded-3 p-4">
            <table class="table align-middle">
                <thead><tr><th>Nom</th><th>Prix</th><th>Quota SMS</th><th>Actif</th><th></th></tr></thead>
                <tbody>
                @forelse ($plans as $plan)
                    <tr>
                        <td>{{ $plan->name }}</td>
                        <td>{{ number_format($plan->price ?? 0, 0, ',', ' ') }} Ar</td>
                        <td>{{ $plan->sms_quota ?? '—' }}</td>
                        <td><span class="badge {{ $plan->active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $plan->active ? 'Actif' : 'Inactif' }}</span></td>
                        <td>
                            <form method="POST" action="{{ route('admin.plans.toggle', $plan) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-outline-primary">{{ $plan->active ? 'Désactiver' : 'Activer' }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-secondary">Aucun plan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
