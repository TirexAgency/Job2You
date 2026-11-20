<x-app-layout>
    <div class="app-container">
        <div>
            <header class="app-page-header">
                <div>
                    <p class="app-eyebrow">Administration</p>
                    <h1 class="h2 fw-bold mb-1">Sources de collecte</h1>
                    <p class="text-secondary mb-0">Gérez les sources de collecte d'offres d'emploi.</p>
                </div>
                <button type="button" class="btn btn-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#createSourceModal" data-auto-open="{{ $errors->any() && !old('_source_id') ? 'true' : 'false' }}">
                    <i class="bi bi-plus-lg me-2" aria-hidden="true"></i>Nouvelle source
                </button>
            </header>

            <!-- Sources Table -->
            <section class="bg-white border rounded-3 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>URL de base</th>
                                <th>Clé collecteur</th>
                                <th>Dernière exécution</th>
                                <th>Actif</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sources as $source)
                                <tr>
                                    <td>{{ $source->name }}</td>
                                    <td class="small text-secondary">
                                        @if ($source->base_url)
                                            <a href="{{ $source->base_url }}" target="_blank" class="text-truncate d-inline-block" style="max-width: 300px;">{{ $source->base_url }}</a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="small text-secondary"><code>{{ $source->collector_key }}</code></td>
                                    <td>{{ $source->last_run_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                    <td>
                                        <span class="badge {{ $source->active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                            {{ $source->active ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" title="Voir" data-bs-toggle="modal" data-bs-target="#showSourceModal{{ $source->id }}">
                                                <i class="bi bi-eye" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" title="Modifier" data-bs-toggle="modal" data-bs-target="#editSourceModal{{ $source->id }}" data-auto-open="{{ $errors->any() && old('_source_id') == $source->id ? 'true' : 'false' }}">
                                                <i class="bi bi-pencil" aria-hidden="true"></i>
                                            </button>
                                            @if($source->active && $source->base_url)
                                                <form method="POST" action="{{ route('admin.sources.scrape', $source) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Lancer la collecte">
                                                        <i class="bi bi-play-circle-fill"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('admin.sources.toggle', $source) }}" class="d-inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-{{ $source->active ? 'warning' : 'success' }}" title="{{ $source->active ? 'Désactiver' : 'Activer' }}">
                                                    <i class="bi bi-{{ $source->active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Supprimer" data-bs-toggle="modal" data-bs-target="#deleteSourceModal{{ $source->id }}">
                                                <i class="bi bi-trash" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-secondary">
                                        <i class="bi bi-inbox fs-1 d-block mb-2" aria-hidden="true"></i>
                                        Aucune source.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($sources->hasPages())
                    <div class="border-top p-3">
                        {{ $sources->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>

    <!-- Create Source Modal -->
    <div class="modal fade" id="createSourceModal" tabindex="-1" aria-labelledby="createSourceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h5 fw-bold" id="createSourceModalLabel">Nouvelle source de collecte</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form method="POST" action="{{ route('admin.sources.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required maxlength="100">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="base_url" class="form-label">URL de base (flux RSS ou API)</label>
                                <input type="url" class="form-control @error('base_url') is-invalid @enderror" id="base_url" name="base_url" value="{{ old('base_url') }}" maxlength="255" placeholder="https://example.com/feed.rss">
                                @error('base_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">URL du flux RSS ou endpoint API pour la collecte d'offres.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="collector_key" class="form-label">Clé collecteur <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('collector_key') is-invalid @enderror" id="collector_key" name="collector_key" value="{{ old('collector_key') }}" required maxlength="100" placeholder="rss, api, rss_indeed, api_francetravail...">
                                @error('collector_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Clé unique identifiant le type de collecteur (rss, api, rss_indeed, api_ft, etc.). Doit correspondre à un collecteur enregistré.</div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input type="checkbox" class="form-check-input" id="active" name="active" {{ old('active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="active">Active</label>
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

    <!-- Edit Source Modals -->
    @foreach($sources as $source)
        <div class="modal fade" id="editSourceModal{{ $source->id }}" tabindex="-1" aria-labelledby="editSourceModal{{ $source->id }}Label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5 fw-bold" id="editSourceModal{{ $source->id }}Label">Modifier {{ $source->name }}</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.sources.update', $source) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_source_id" value="{{ $source->id }}">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="edit_name_{{ $source->id }}" class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_name_{{ $source->id }}" name="name" value="{{ old('name', $source->name) }}" required maxlength="100">
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_base_url_{{ $source->id }}" class="form-label">URL de base (flux RSS ou API)</label>
                                    <input type="url" class="form-control" id="edit_base_url_{{ $source->id }}" name="base_url" value="{{ old('base_url', $source->base_url) }}" maxlength="255" placeholder="https://example.com/feed.rss">
                                    <div class="form-text">URL du flux RSS ou endpoint API pour la collecte d'offres.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_collector_key_{{ $source->id }}" class="form-label">Clé collecteur <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_collector_key_{{ $source->id }}" name="collector_key" value="{{ old('collector_key', $source->collector_key) }}" required maxlength="100" placeholder="rss, api, rss_indeed, api_francetravail...">
                                    <div class="form-text">Clé unique identifiant le type de collecteur (rss, api, rss_indeed, api_ft, etc.). Doit correspondre à un collecteur enregistré.</div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" class="form-check-input" id="edit_active_{{ $source->id }}" name="active" {{ old('active', $source->active) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-medium" for="edit_active_{{ $source->id }}">Active</label>
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

    <!-- Show Source Modals -->
    @foreach($sources as $source)
        <div class="modal fade" id="showSourceModal{{ $source->id }}" tabindex="-1" aria-labelledby="showSourceModal{{ $source->id }}Label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5 fw-bold" id="showSourceModal{{ $source->id }}Label">{{ $source->name }}</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-3 text-secondary">Nom</dt>
                            <dd class="col-sm-9">{{ $source->name }}</dd>

                            <dt class="col-sm-3 text-secondary">URL de base</dt>
                            <dd class="col-sm-9">
                                @if ($source->base_url)
                                    <a href="{{ $source->base_url }}" target="_blank" class="text-truncate d-inline-block" style="max-width: 100%;">{{ $source->base_url }}</a>
                                @else
                                    <span class="text-secondary">—</span>
                                @endif
                            </dd>

                            <dt class="col-sm-3 text-secondary">Clé collecteur</dt>
                            <dd class="col-sm-9"><code>{{ $source->collector_key }}</code></dd>

                            <dt class="col-sm-3 text-secondary">Statut</dt>
                            <dd class="col-sm-9">
                                <span class="badge {{ $source->active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $source->active ? 'Actif' : 'Inactif' }}
                                </span>
                            </dd>

                            <dt class="col-sm-3 text-secondary">Dernière exécution</dt>
                            <dd class="col-sm-9">{{ $source->last_run_at?->format('d/m/Y H:i') ?? 'Jamais' }}</dd>

                            <dt class="col-sm-3 text-secondary">Créée le</dt>
                            <dd class="col-sm-9">{{ $source->created_at->format('d/m/Y H:i') }}</dd>

                            <dt class="col-sm-3 text-secondary">Modifiée le</dt>
                            <dd class="col-sm-9">{{ $source->updated_at->format('d/m/Y H:i') }}</dd>
                        </dl>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editSourceModal{{ $source->id }}">Modifier</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @foreach($sources as $source)
        <x-confirm-modal
            id="deleteSourceModal{{ $source->id }}"
            title="Supprimer la source"
            :action="route('admin.sources.destroy', $source)"
            confirm-label="Supprimer"
            :message="'La source « ' . $source->name . ' » sera définitivement supprimée.'"
        />
    @endforeach

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelector('[data-auto-open="true"]')?.click();
            });
        </script>
    @endif
</x-app-layout>