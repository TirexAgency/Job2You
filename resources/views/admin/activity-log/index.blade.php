<x-app-layout>
    <div class="app-container">
        <div>
            <header class="app-page-header">
                <div>
                    <p class="app-eyebrow">Administration</p>
                    <h1 class="h2 fw-bold mb-1">Journal d'activité</h1>
                    <p class="text-secondary mb-0">Suivi des actions de collecte, nettoyage et administration.</p>
                </div>
            </header>

            <!-- Filtres -->
            <section class="bg-white border rounded-3 shadow-sm p-4 mb-4">
                <form method="GET" action="{{ route('admin.activity-log.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Rechercher</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Filtrer par source, message, etc.">
                    </div>
                    <div class="col-md-2">
                        <label for="level" class="form-label">Niveau</label>
                        <select class="form-select" id="level" name="level">
                            <option value="">Tous les niveaux</option>
                            <option value="info" {{ $level === 'info' ? 'selected' : '' }}>Info</option>
                            <option value="warning" {{ $level === 'warning' ? 'selected' : '' }}>Attention</option>
                            <option value="error" {{ $level === 'error' ? 'selected' : '' }}>Erreur</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="lines" class="form-label">Lignes</label>
                        <select class="form-select" id="lines" name="lines">
                            @foreach ($lineOptions as $option)
                                <option value="{{ $option }}" {{ $lines === $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary w-100">Filtrer</button>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <a href="{{ route('admin.activity-log.index') }}" class="btn btn-outline-secondary w-100">Réinitialiser</a>
                    </div>
                </form>
            </section>

            <div class="d-flex flex-wrap gap-2 mb-4">
                <span class="badge bg-info-subtle text-info-emphasis">Info : {{ $levelCounts['info'] ?? 0 }}</span>
                <span class="badge bg-warning-subtle text-warning-emphasis">Attention : {{ $levelCounts['warning'] ?? 0 }}</span>
                <span class="badge bg-danger-subtle text-danger-emphasis">Erreur : {{ $levelCounts['error'] ?? 0 }}</span>
                <span class="badge bg-light text-secondary">{{ $parsedLines ? count($parsedLines) : 0 }} entrée(s) affichée(s)</span>
            </div>

            <!-- Logs Table -->
            <section class="bg-white border rounded-3 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size: 0.875rem;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 160px;">Horodatage</th>
                                <th style="width: 100px;">Niveau</th>
                                <th>Message</th>
                                <th style="width: 300px;">Détails</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($parsedLines as $log)
                                <tr class="{{ $log['level'] === 'error' ? 'table-danger' : ($log['level'] === 'warning' ? 'table-warning' : '') }}">
                                    <td class="text-nowrap small text-secondary">{{ $log['time'] }}</td>
                                    <td>
                                        <span class="badge {{ $log['level'] === 'error' ? 'bg-danger' : ($log['level'] === 'warning' ? 'bg-warning text-dark' : 'bg-info') }}">
                                            {{ ucfirst($log['level']) }}
                                        </span>
                                    </td>
                                    <td class="fw-medium">{{ $log['message'] }}</td>
                                    <td>
                                        @if(!empty($log['context']))
                                            <div class="small text-secondary">
                                                @foreach($log['context'] as $key => $value)
                                                    @if(is_array($value) || is_object($value))
                                                        <div><strong>{{ $key }}:</strong> <code>{{ json_encode($value) }}</code></div>
                                                    @else
                                                        <div><strong>{{ $key }}:</strong> {{ $value }}</div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-secondary">
                                        <i class="bi bi-journal-text fs-1 d-block mb-2" aria-hidden="true"></i>
                                        Aucun log d'activité trouvé.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>