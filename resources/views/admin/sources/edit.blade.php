<x-app-layout>
    <div class="container py-4" style="max-width: 800px;">
        <h1 class="h3 fw-bold mb-4">Modifier la source : {{ $source->name }}</h1>

        <div class="bg-white border rounded-3 p-4">
            <form method="POST" action="{{ route('admin.sources.update', $source) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label fw-medium">Nom <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $source->name) }}" required maxlength="100">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="base_url" class="form-label fw-medium">URL de base (flux RSS ou API)</label>
                    <input type="url" class="form-control @error('base_url') is-invalid @enderror" id="base_url" name="base_url" value="{{ old('base_url', $source->base_url) }}" maxlength="255" placeholder="https://example.com/feed.rss">
                    @error('base_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">URL du flux RSS ou endpoint API pour la collecte d'offres.</div>
                </div>

                <div class="mb-3">
                    <label for="collector_key" class="form-label fw-medium">Clé collecteur <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('collector_key') is-invalid @enderror" id="collector_key" name="collector_key" value="{{ old('collector_key', $source->collector_key) }}" required maxlength="100" placeholder="rss, api, rss_indeed, api_francetravail...">
                    @error('collector_key')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Clé unique identifiant le type de collecteur (rss, api, rss_indeed, api_ft, etc.). Doit correspondre à un collecteur enregistré.</div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="active" name="active" {{ old('active', $source->active) ? 'checked' : '' }}>
                    <label class="form-check-label fw-medium" for="active">Active</label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('admin.sources.index') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>