<x-app-layout>
    <div class="app-container">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Espace candidat</p>
                <h1 class="h2 fw-bold mb-1">Mon CV — Analyse IA</h1>
                <p class="text-secondary mb-0">Importez votre CV pour enrichir automatiquement votre profil.</p>
            </div>
            <a href="{{ route('candidate.profile.edit') }}#t-info" class="btn btn-outline-primary align-self-start align-self-md-center">
                <i class="bi bi-person me-1" aria-hidden="true"></i>Voir mon profil
            </a>
        </header>

        @if (! $cvParsingEnabled)
            <div class="alert alert-warning d-flex align-items-start gap-2" role="status">
                <i class="bi bi-exclamation-triangle-fill alert-icon mt-1" aria-hidden="true"></i>
                <div>
                    Votre formule actuelle n’inclut pas l’analyse de CV par IA.
                    <a href="{{ route('candidate.subscription') }}" class="alert-link">Voir les formules disponibles</a>.
                </div>
            </div>
        @else
            <section class="app-card mb-4">
                <div class="app-card-header">
                    <div>
                        <h2 class="app-section-title">Importer un CV</h2>
                        <p class="small text-secondary mb-0">Formats acceptés : PDF, DOC ou DOCX (5 Mo maximum).</p>
                    </div>
                </div>
                <div class="app-card-body">
                    <form method="POST" action="{{ route('candidate.cv.upload') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="cv" class="form-label">Fichier CV</label>
                            <input type="file" id="cv" name="cv" class="form-control @error('cv') is-invalid @enderror" accept=".pdf,.doc,.docx" required>
                            @error('cv')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button class="btn btn-primary">
                            <i class="bi bi-cloud-upload me-1" aria-hidden="true"></i>Envoyer pour analyse
                        </button>
                    </form>
                </div>
            </section>
        @endif

        <section class="app-card">
            <div class="app-card-header">
                <div>
                    <h2 class="app-section-title">Analyses</h2>
                    <p class="small text-secondary mb-0">Historique des CV importés et de leur traitement.</p>
                </div>
                <span class="badge bg-primary-subtle text-primary-emphasis">{{ $parses->count() }} analyse(s)</span>
            </div>

            <div class="app-card-body p-0">
                @forelse ($parses as $parse)
                    <div class="px-4 py-3 {{ ! $loop->last ? 'border-bottom' : '' }}">
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-2">
                            <span class="small text-secondary">
                                <i class="bi bi-calendar3 me-1" aria-hidden="true"></i>{{ $parse->created_at->format('d/m/Y H:i') }}
                            </span>
                            <span class="badge {{ $parse->status === 'done' ? 'text-bg-success' : ($parse->status === 'failed' ? 'text-bg-danger' : 'text-bg-warning') }}">{{ ucfirst($parse->status) }}</span>
                        </div>

                        @if ($parse->extracted_json)
                            <pre class="bg-light border rounded-3 p-3 small mb-2" style="max-height: 16rem; overflow: auto;">{{ json_encode($parse->extracted_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        @endif

                        @if ($parse->status !== 'done' && $cvParsingEnabled)
                            <form method="POST" action="{{ route('candidate.cv.validate', $parse) }}">
                                @csrf
                                <button class="btn btn-sm btn-outline-primary">Valider / corriger plus tard</button>
                            </form>
                        @endif

                        @if ($parse->validated_at)
                            <p class="small text-success mb-0 mt-2">
                                <i class="bi bi-check-circle me-1" aria-hidden="true"></i>Validé le {{ $parse->validated_at->format('d/m/Y') }}
                            </p>
                        @endif
                    </div>
                @empty
                    <div class="app-empty-state">
                        <span class="app-empty-icon" aria-hidden="true"><i class="bi bi-file-earmark-text"></i></span>
                        <h3 class="h5 fw-bold mb-2">Aucune analyse pour le moment</h3>
                        <p class="text-secondary mb-0">Importez un CV pour lancer votre première analyse.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
