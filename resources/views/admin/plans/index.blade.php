<x-app-layout>
    <div class="app-container">
        <div>
            <header class="app-page-header">
                <div>
                    <p class="app-eyebrow">Administration</p>
                    <h1 class="h2 fw-bold mb-1">Plans d’abonnement</h1>
                    <p class="text-secondary mb-0">Définissez les formules, leurs prix et les quotas associés.</p>
                </div>
                <button type="button" class="btn btn-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#createPlanModal" data-auto-open="{{ $errors->any() && ! old('_plan_id') ? 'true' : 'false' }}">
                    <i class="bi bi-plus-lg me-2" aria-hidden="true"></i>Nouveau plan
                </button>
            </header>

            <section class="bg-white border rounded-3 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Plan</th>
                                <th>Prix</th>
                                <th>Quota SMS</th>
                                <th>Analyse de CV</th>
                                <th>Abonnés actifs</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($plans as $plan)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $plan->name }}</div>
                                        <div class="small text-secondary">
                                            {{ $plan->duration_days > 0 ? $plan->duration_days.' jours' : 'Sans expiration' }}
                                        </div>
                                    </td>
                                    <td class="fw-semibold">{{ number_format((float) $plan->price, 0, ',', ' ') }} Ar</td>
                                    <td>{{ number_format($plan->sms_quota, 0, ',', ' ') }} SMS</td>
                                    <td>
                                        <span class="badge {{ $plan->cv_parsing_enabled ? 'text-bg-success' : 'text-bg-secondary' }}">
                                            {{ $plan->cv_parsing_enabled ? 'Incluse' : 'Non incluse' }}
                                        </span>
                                    </td>
                                    <td>{{ $plan->active_subscriptions_count }}</td>
                                    <td>
                                        <span class="badge {{ $plan->active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                            {{ $plan->active ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" title="Voir" data-bs-toggle="modal" data-bs-target="#showPlanModal{{ $plan->id }}">
                                                <i class="bi bi-eye" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" title="Modifier" data-bs-toggle="modal" data-bs-target="#editPlanModal{{ $plan->id }}" data-auto-open="{{ $errors->any() && old('_plan_id') == $plan->id ? 'true' : 'false' }}">
                                                <i class="bi bi-pencil" aria-hidden="true"></i>
                                            </button>
                                            <form method="POST" action="{{ route('admin.plans.toggle', $plan) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-{{ $plan->active ? 'warning' : 'success' }}" title="{{ $plan->active ? 'Désactiver' : 'Activer' }}">
                                                    <i class="bi bi-{{ $plan->active ? 'pause' : 'play' }}" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                            @if ($plan->subscriptions_count === 0 && $plan->payments_count === 0)
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Supprimer" data-bs-toggle="modal" data-bs-target="#deletePlanModal{{ $plan->id }}">
                                                    <i class="bi bi-trash" aria-hidden="true"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Ce plan est utilisé par des abonnements ou des paiements" disabled>
                                                    <i class="bi bi-trash" aria-hidden="true"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
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
                @if ($plans->hasPages())
                    <div class="border-top p-3">
                        {{ $plans->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>

    <!-- Create Plan Modal -->
    <div class="modal fade" id="createPlanModal" tabindex="-1" aria-labelledby="createPlanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h5 fw-bold" id="createPlanModalLabel">Nouveau plan d’abonnement</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form method="POST" action="{{ route('admin.plans.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required maxlength="100" placeholder="free, premium, pro…">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="price" class="form-label">Prix (Ar) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', 0) }}" required min="0" step="0.01">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="duration_days" class="form-label">Durée (jours) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('duration_days') is-invalid @enderror" id="duration_days" name="duration_days" value="{{ old('duration_days', 30) }}" required min="0" max="3650">
                                @error('duration_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">0 pour un plan sans expiration.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="sms_quota" class="form-label">Quota SMS <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('sms_quota') is-invalid @enderror" id="sms_quota" name="sms_quota" value="{{ old('sms_quota', 0) }}" required min="0" max="100000">
                                @error('sms_quota')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input type="hidden" name="cv_parsing_enabled" value="0">
                                    <input type="checkbox" class="form-check-input" id="cv_parsing_enabled" name="cv_parsing_enabled" value="1" {{ old('cv_parsing_enabled') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="cv_parsing_enabled">Analyse de CV incluse</label>
                                </div>
                                <div class="form-check mt-2">
                                    <input type="hidden" name="active" value="0">
                                    <input type="checkbox" class="form-check-input" id="active" name="active" value="1" {{ old('active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="active">Plan actif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Créer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Plan Modals -->
    @foreach ($plans as $plan)
        <div class="modal fade" id="editPlanModal{{ $plan->id }}" tabindex="-1" aria-labelledby="editPlanModal{{ $plan->id }}Label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5 fw-bold" id="editPlanModal{{ $plan->id }}Label">Modifier {{ $plan->name }}</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.plans.update', $plan) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_plan_id" value="{{ $plan->id }}">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="edit_name_{{ $plan->id }}" class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_name_{{ $plan->id }}" name="name" value="{{ old('name', $plan->name) }}" required maxlength="100">
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_price_{{ $plan->id }}" class="form-label">Prix (Ar) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="edit_price_{{ $plan->id }}" name="price" value="{{ old('price', $plan->price) }}" required min="0" step="0.01">
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_duration_days_{{ $plan->id }}" class="form-label">Durée (jours) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="edit_duration_days_{{ $plan->id }}" name="duration_days" value="{{ old('duration_days', $plan->duration_days) }}" required min="0" max="3650">
                                    <div class="form-text">0 pour un plan sans expiration.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_sms_quota_{{ $plan->id }}" class="form-label">Quota SMS <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="edit_sms_quota_{{ $plan->id }}" name="sms_quota" value="{{ old('sms_quota', $plan->sms_quota) }}" required min="0" max="100000">
                                </div>

                                <div class="col-12">
                                    <div class="form-check">
                                        <input type="hidden" name="cv_parsing_enabled" value="0">
                                        <input type="checkbox" class="form-check-input" id="edit_cv_parsing_enabled_{{ $plan->id }}" name="cv_parsing_enabled" value="1" {{ old('cv_parsing_enabled', $plan->cv_parsing_enabled) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-medium" for="edit_cv_parsing_enabled_{{ $plan->id }}">Analyse de CV incluse</label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input type="hidden" name="active" value="0">
                                        <input type="checkbox" class="form-check-input" id="edit_active_{{ $plan->id }}" name="active" value="1" {{ old('active', $plan->active) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-medium" for="edit_active_{{ $plan->id }}">Plan actif</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Show Plan Modals -->
    @foreach ($plans as $plan)
        <div class="modal fade" id="showPlanModal{{ $plan->id }}" tabindex="-1" aria-labelledby="showPlanModal{{ $plan->id }}Label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5 fw-bold" id="showPlanModal{{ $plan->id }}Label">{{ $plan->name }}</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-secondary">Nom</dt>
                            <dd class="col-sm-8">{{ $plan->name }}</dd>

                            <dt class="col-sm-4 text-secondary">Prix</dt>
                            <dd class="col-sm-8">{{ number_format((float) $plan->price, 0, ',', ' ') }} Ar</dd>

                            <dt class="col-sm-4 text-secondary">Durée</dt>
                            <dd class="col-sm-8">{{ $plan->duration_days > 0 ? $plan->duration_days.' jours' : 'Sans expiration' }}</dd>

                            <dt class="col-sm-4 text-secondary">Quota SMS</dt>
                            <dd class="col-sm-8">{{ number_format($plan->sms_quota, 0, ',', ' ') }} SMS</dd>

                            <dt class="col-sm-4 text-secondary">Analyse de CV</dt>
                            <dd class="col-sm-8">{{ $plan->cv_parsing_enabled ? 'Incluse' : 'Non incluse' }}</dd>

                            <dt class="col-sm-4 text-secondary">Abonnés actifs</dt>
                            <dd class="col-sm-8">{{ $plan->active_subscriptions_count }}</dd>

                            <dt class="col-sm-4 text-secondary">Statut</dt>
                            <dd class="col-sm-8">
                                <span class="badge {{ $plan->active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $plan->active ? 'Actif' : 'Inactif' }}
                                </span>
                            </dd>

                            <dt class="col-sm-4 text-secondary">Créé le</dt>
                            <dd class="col-sm-8">{{ $plan->created_at->format('d/m/Y H:i') }}</dd>

                            <dt class="col-sm-4 text-secondary">Modifié le</dt>
                            <dd class="col-sm-8">{{ $plan->updated_at->format('d/m/Y H:i') }}</dd>
                        </dl>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editPlanModal{{ $plan->id }}">Modifier</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @foreach($plans as $plan)
        @continue($plan->subscriptions_count !== 0 || $plan->payments_count !== 0)
        <x-confirm-modal
            id="deletePlanModal{{ $plan->id }}"
            title="Supprimer le plan"
            :action="route('admin.plans.destroy', $plan)"
            confirm-label="Supprimer"
            :message="'Le plan « ' . $plan->name . ' » sera définitivement supprimé.'"
        />
    @endforeach

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelector('[data-auto-open="true"]')?.click();
            });
        </script>
    @endif
</x-app-layout>
