<x-app-layout>
    <div class="container py-4" style="max-width: 1200px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold mb-0">Logs & Supervision</h1>
            <div class="btn-group" role="group">
                <a href="{{ route('admin.activity-log.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-journal-text me-1"></i> Journal d'activité (métier - filtres)
                </a>
                <a href="{{ route('admin.logs.index') }}" class="btn btn-outline-secondary active" aria-current="page">
                    <i class="bi bi-terminal me-1"></i> Logs complets (brut)
                </a>
            </div>
        </div>

        <div class="bg-white border rounded-3 p-4">
            <p class="text-secondary mb-2">
                Dernières entrées du journal d'activité métier (<code>storage/logs/activity.log</code>) :
            </p>
            <form method="GET" class="mb-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label for="lines-count" class="form-label small mb-1">Nombre de lignes</label>
                        <select id="lines-count" name="lines" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="50" {{ request('lines') == '50' ? 'selected' : '' }}>50 lignes</option>
                            <option value="100" {{ request('lines') == '100' ? 'selected' : '' }}>100 lignes</option>
                            <option value="200" {{ request('lines') == '200' ? 'selected' : '' }}>200 lignes</option>
                            <option value="500" {{ request('lines') == '500' ? 'selected' : '' }}>500 lignes</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="level-filter" class="form-label small mb-1">Niveau</label>
                        <select id="level-filter" name="level" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Tous les niveaux</option>
                            <option value="info" {{ request('level') === 'info' ? 'selected' : '' }}>Info</option>
                            <option value="warning" {{ request('level') === 'warning' ? 'selected' : '' }}>Attention</option>
                            <option value="error" {{ request('level') === 'error' ? 'selected' : '' }}>Erreur</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="search-filter" class="form-label small mb-1">Recherche</label>
                        <input type="text" id="search-filter" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Filtrer (source, message...)" onchange="this.form.submit()">
                    </div>
                </div>
            </form>
            <pre class="bg-dark text-light border rounded p-3 small" style="max-height: 500px; overflow:auto;">{{ $logContent ?? 'Fichier de log introuvable.' }}</pre>
        </div>
    </div>
</x-app-layout>