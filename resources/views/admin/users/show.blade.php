<x-app-layout>
    <div class="app-container">
        <div class="mx-auto" style="max-width: 960px;">
            <header class="app-page-header">
                <div>
                    <p class="app-eyebrow">Administration</p>
                    <h1 class="h2 fw-bold mb-1">{{ $user->name }}</h1>
                    <p class="text-secondary mb-0">Détails du compte, abonnements et paiements.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2" aria-hidden="true"></i>Modifier
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Retour</a>
                </div>
            </header>

            <section class="bg-white border rounded-3 shadow-sm p-4 p-lg-5 mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                    <h2 class="h5 fw-bold mb-0">Informations personnelles</h2>
                    @if ($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.status', $user) }}" class="d-flex align-items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <label for="status" class="form-label small mb-0">Statut</label>
                            <select id="status" name="status" class="form-select form-select-sm" style="width: auto;">
                                @foreach (\App\Models\User::statusLabels() as $value => $label)
                                    <option value="{{ $value }}" {{ $user->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-primary">Appliquer</button>
                        </form>
                    @endif
                </div>

                <dl class="row mb-0">
                    <dt class="col-sm-4 text-secondary">Nom complet</dt>
                    <dd class="col-sm-8 fw-semibold">{{ $user->name }}</dd>

                    <dt class="col-sm-4 text-secondary">Adresse email</dt>
                    <dd class="col-sm-8">{{ $user->email }}</dd>

                    <dt class="col-sm-4 text-secondary">Téléphone</dt>
                    <dd class="col-sm-8">{{ $user->phone ?: 'Non renseigné' }}</dd>

                    <dt class="col-sm-4 text-secondary">Rôle</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'recruiter' ? 'warning' : 'info') }}">
                            {{ $user->role_label }}
                        </span>
                    </dd>

                    <dt class="col-sm-4 text-secondary">Statut</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'pending' ? 'warning' : 'secondary') }}">
                            {{ $user->status_label }}
                        </span>
                    </dd>

                    <dt class="col-sm-4 text-secondary">Email vérifié</dt>
                    <dd class="col-sm-8">
                        @if ($user->email_verified_at)
                            <span class="text-success"><i class="bi bi-check-circle me-1" aria-hidden="true"></i>Oui, le {{ $user->email_verified_at->format('d/m/Y H:i') }}</span>
                        @else
                            <span class="text-warning"><i class="bi bi-exclamation-circle me-1" aria-hidden="true"></i>Non vérifié</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4 text-secondary">Compte créé le</dt>
                    <dd class="col-sm-8">{{ $user->created_at->format('d/m/Y H:i') }}</dd>

                    <dt class="col-sm-4 text-secondary">Dernière mise à jour</dt>
                    <dd class="col-sm-8">{{ $user->updated_at->format('d/m/Y H:i') }}</dd>
                </dl>
            </section>

            <section class="bg-white border rounded-3 shadow-sm p-4 p-lg-5 mb-4">
                <h2 class="h5 fw-bold mb-4">Abonnement & SMS</h2>
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-secondary">Formule</dt>
                    <dd class="col-sm-8 fw-semibold">{{ $user->planDisplayName() }}</dd>

                    <dt class="col-sm-4 text-secondary">Quota SMS</dt>
                    <dd class="col-sm-8">{{ $user->getSmsQuotaLimit() }}</dd>

                    <dt class="col-sm-4 text-secondary">SMS restants</dt>
                    <dd class="col-sm-8 fw-semibold text-primary">{{ $user->getSmsRemaining() }}</dd>
                </dl>

                <hr class="my-4">

                <h3 class="h6 fw-bold mb-3">Derniers abonnements</h3>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Formule</th>
                                <th>Statut</th>
                                <th>Début</th>
                                <th>Fin</th>
                                <th class="text-end">SMS restants</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($user->subscriptions as $subscription)
                                <tr>
                                    <td>{{ $subscription->plan?->name ?? '—' }}</td>
                                    <td><span class="badge {{ $subscription->isActive() ? 'text-bg-success' : 'text-bg-secondary' }}">{{ ucfirst($subscription->status) }}</span></td>
                                    <td class="small text-secondary">{{ $subscription->starts_at?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="small text-secondary">{{ $subscription->ends_at?->format('d/m/Y') ?? 'Sans expiration' }}</td>
                                    <td class="text-end">{{ $subscription->smsRemaining() }} / {{ $subscription->smsQuota() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-secondary py-3">Aucun abonnement</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="bg-white border rounded-3 shadow-sm p-4 p-lg-5">
                <h2 class="h5 fw-bold mb-4">Derniers paiements</h2>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Référence</th>
                                <th>Formule</th>
                                <th class="text-end">Montant</th>
                                <th>Statut</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($user->payments as $payment)
                                <tr>
                                    <td class="small"><code>{{ $payment->payload_reference ?: $payment->provider_reference }}</code></td>
                                    <td>{{ $payment->plan?->name ?? '—' }}</td>
                                    <td class="text-end">{{ number_format((float) $payment->amount, 0, ',', ' ') }} Ar</td>
                                    <td><span class="badge {{ $payment->status === 'paid' ? 'text-bg-success' : ($payment->status === 'failed' ? 'text-bg-danger' : 'text-bg-secondary') }}">{{ \App\Models\Payment::statusLabels()[$payment->status] ?? $payment->status }}</span></td>
                                    <td class="small text-secondary">{{ $payment->created_at?->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-secondary py-3">Aucun paiement</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
