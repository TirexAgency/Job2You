<x-app-layout>
    <div class="container py-4" style="max-width: 1200px;">
        <h1 class="h3 fw-bold mb-4">Plans d'abonnement</h1>
        <div class="bg-white border rounded-3 p-4">
            <table class="table align-middle">
                <thead><tr><th>Nom</th><th>Prix</th><th>Quota SMS</th></tr></thead>
                <tbody>
                @forelse ($plans as $plan)
                    <tr>
                        <td>{{ $plan->name }}</td>
                        <td>{{ number_format($plan->price ?? 0, 0, ',', ' ') }} Ar</td>
                        <td>{{ $plan->sms_quota ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-secondary">Aucun plan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
