<x-app-layout>
    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Administration</p>
                <h1 class="h2 fw-bold mb-1">Paramètres de matching</h1>
                <p class="text-secondary mb-0">Ajustez les pondérations et le seuil utilisés pour calculer les correspondances candidat / offre.</p>
            </div>
        </header>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="d-flex flex-column gap-4">
            @csrf
            @method('PUT')

            <section class="app-card p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                    <div>
                        <h2 class="h5 fw-bold mb-1">Pondérations des critères</h2>
                        <p class="text-secondary small mb-0">Chaque pondération est comprise entre 0 et 1. La somme des cinq critères doit être égale à 1.0.</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary-emphasis">Score sur 100 %</span>
                </div>

                <div class="row g-4">
                    @foreach ($weightFields as $field)
                        <div class="col-md-6">
                            <label for="weight_{{ $field['field'] }}" class="form-label fw-semibold">{{ $field['label'] }}</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                max="1"
                                class="form-control @error('weights.'.$field['field']) is-invalid @enderror"
                                id="weight_{{ $field['field'] }}"
                                name="weights[{{ $field['field'] }}]"
                                value="{{ old('weights.'.$field['field'], $field['value']) }}"
                                required
                            >
                            @error('weights.'.$field['field'])
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if ($field['description'])
                                <div class="form-text">{{ $field['description'] }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>

                @error('weights')
                    <div class="alert alert-danger d-flex align-items-start gap-2 mt-4 mb-0" role="alert">
                        <i class="bi bi-exclamation-octagon-fill alert-icon mt-1" aria-hidden="true"></i>
                        <div>{{ $message }}</div>
                    </div>
                @enderror
            </section>

            <section class="app-card p-4">
                <h2 class="h5 fw-bold mb-1">Seuil d’alerte SMS</h2>
                <p class="text-secondary small mb-4">Un SMS est envoyé au candidat lorsque le score de correspondance atteint ou dépasse ce seuil.</p>

                <div class="row">
                    <div class="col-md-6">
                        <label for="alert_threshold" class="form-label fw-semibold">{{ $threshold['label'] }}</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            max="1"
                            class="form-control @error('alert_threshold') is-invalid @enderror"
                            id="alert_threshold"
                            name="alert_threshold"
                            value="{{ old('alert_threshold', $threshold['value']) }}"
                            required
                        >
                        @error('alert_threshold')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">{{ $threshold['description'] ?? 'Score minimum (0 à 1) pour déclencher une alerte SMS.' }}</div>
                    </div>
                </div>
            </section>

            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check2 me-2" aria-hidden="true"></i>Enregistrer les paramètres
                </button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary px-4">Annuler</a>
            </div>
        </form>
    </div>
</x-app-layout>
