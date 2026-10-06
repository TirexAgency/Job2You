<x-app-layout>
    <div class="container py-4" style="max-width: 900px;">
        <h1 class="h3 fw-bold mb-4">Mon CV — Analyse IA</h1>

        @if (session('status') === 'cv-uploaded')
            <div class="alert alert-success">CV importé. L'analyse sera lancée (statut : en attente).</div>
        @elseif (session('status') === 'cv-validated')
            <div class="alert alert-success">Données du CV validées.</div>
        @endif

        @if (! $cvParsingEnabled)
            <div class="alert alert-warning">Votre offre actuelle n'inclut pas l'analyse de CV par IA. <a href="{{ route('candidate.subscription') }}">Passer à une offre Premium</a>.</div>
        @else
            <div class="bg-white border rounded-3 p-4 mb-4">
                <h2 class="h5 fw-bold mb-3">Importer un CV</h2>
                <form method="POST" action="{{ route('candidate.cv.upload') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <input type="file" name="cv" class="form-control" accept=".pdf,.doc,.docx" required>
                        <div class="form-text">PDF, DOC ou DOCX, 5 Mo maximum.</div>
                        @error('cv')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <button class="btn btn-primary">Envoyer pour analyse</button>
                </form>
            </div>
        @endif

        <h2 class="h5 fw-bold mb-3">Analyses</h2>
        @forelse ($parses as $parse)
            <div class="bg-white border rounded-3 p-4 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small text-secondary">{{ $parse->created_at->format('d/m/Y H:i') }}</span>
                    <span class="badge {{ $parse->status === 'done' ? 'text-bg-success' : ($parse->status === 'failed' ? 'text-bg-danger' : 'text-bg-warning') }}">{{ ucfirst($parse->status) }}</span>
                </div>
                @if ($parse->extracted_json)
                    <pre class="bg-light border rounded p-2 small">{{ json_encode($parse->extracted_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @endif
                @if ($parse->status !== 'done' && $cvParsingEnabled)
                    <form method="POST" action="{{ route('candidate.cv.validate', $parse) }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-primary">Valider / corriger plus tard</button>
                    </form>
                @endif
                @if ($parse->validated_at)
                    <p class="small text-success mb-0">Validé le {{ $parse->validated_at->format('d/m/Y') }}</p>
                @endif
            </div>
        @empty
            <p class="text-secondary">Aucune analyse pour le moment.</p>
        @endforelse
    </div>
</x-app-layout>
