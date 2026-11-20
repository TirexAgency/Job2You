<x-app-layout>
    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Administration</p>
                <h1 class="h2 fw-bold mb-0">Logs & supervision</h1>
                <p class="text-secondary mb-0">Consultez le journal brut et les événements métier.</p>
            </div>
            <div class="btn-group" role="group">
                <a href="{{ route('admin.activity-log.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-journal-text me-1" aria-hidden="true"></i>Journal d’activité
                </a>
                <a href="{{ route('admin.logs.index') }}" class="btn btn-outline-secondary active" aria-current="page">
                    <i class="bi bi-terminal me-1" aria-hidden="true"></i>Logs bruts
                </a>
            </div>
        </header>

        <section class="row g-3 mb-4">
            @foreach (['info' => 'Info', 'warning' => 'Attention', 'error' => 'Erreur'] as $levelKey => $levelLabel)
                <div class="col-sm-4">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <p class="text-secondary small mb-1">{{ $levelLabel }}</p>
                            <p class="h3 mb-0">{{ number_format($levelCounts[$levelKey] ?? 0, 0, ',', ' ') }}</p>
                            <p class="small text-secondary mb-0">entrées affichées</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </section>

        <div class="app-card p-4">
            <p class="text-secondary mb-2">
                Fichier : <code>{{ $logFile ?? 'introuvable' }}</code>
            </p>

            <form method="GET" class="mb-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label for="source" class="form-label small mb-1">Journal</label>
                        <select id="source" name="source" class="form-select form-select-sm">
                            <option value="activity" {{ $source === 'activity' ? 'selected' : '' }}>Activité métier</option>
                            <option value="laravel" {{ $source === 'laravel' ? 'selected' : '' }}>Application (Laravel)</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="lines-count" class="form-label small mb-1">Nombre de lignes</label>
                        <select id="lines-count" name="lines" class="form-select form-select-sm">
                            @foreach ($lineOptions as $option)
                                <option value="{{ $option }}" {{ $linesCount === $option ? 'selected' : '' }}>{{ $option }} lignes</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="level-filter" class="form-label small mb-1">Niveau</label>
                        <select id="level-filter" name="level" class="form-select form-select-sm">
                            <option value="">Tous les niveaux</option>
                            @foreach (['info' => 'Info', 'warning' => 'Attention', 'error' => 'Erreur'] as $levelKey => $levelLabel)
                                <option value="{{ $levelKey }}" {{ $level === $levelKey ? 'selected' : '' }}>{{ $levelLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="search-filter" class="form-label small mb-1">Recherche</label>
                        <input type="text" id="search-filter" name="search" class="form-control form-control-sm" value="{{ $search }}" placeholder="Filtrer (source, message…)">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100">Appliquer</button>
                    </div>
                </div>
            </form>

            <pre class="app-log-output">{{ $logContent ?: 'Aucune entrée ne correspond aux filtres.' }}</pre>
        </div>
    </div>
</x-app-layout>
