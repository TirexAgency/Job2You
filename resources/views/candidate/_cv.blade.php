<section class="bg-white border rounded-3 p-4 p-md-5 mb-4">
    <div class="d-flex flex-column flex-md-row align-items-center gap-4 border-bottom pb-4 mb-4">
        @if (auth()->user()->photo_path)
            <img src="{{ asset('storage/'.auth()->user()->photo_path) }}" alt="Photo de {{ auth()->user()->name }}" class="rounded-circle shadow-sm border border-3 border-white" style="width: 120px; height: 120px; object-fit: cover;">
        @else
            <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 120px; height: 120px; font-size: 3rem;">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
        @endif
        <div class="text-center text-md-start flex-grow-1">
            <h2 class="h3 fw-bold mb-1">{{ auth()->user()->name }}</h2>
            <p class="text-primary fw-semibold mb-2">{{ $profile->desired_jobs ?: 'Candidat' }} · {{ ucfirst($profile->experience_level) }}</p>
            <p class="small text-secondary mb-0">
                <i class="bi bi-geo-alt me-1"></i>{{ $profile->city }}{{ $profile->region ? ', '.$profile->region : '' }}
                <i class="bi bi-envelope ms-3 me-1"></i>{{ auth()->user()->email }}
                @if (auth()->user()->phone)
                    <i class="bi bi-telephone ms-3 me-1"></i>{{ auth()->user()->phone }}
                @endif
            </p>
        </div>
    </div>

    <h3 class="h6 fw-bold text-uppercase text-primary">Compétences</h3>
    <div class="d-flex flex-wrap gap-2 mb-4">
        @forelse ($profile->candidateSkills as $candidateSkill)
            <span class="badge text-bg-light border text-dark">{{ $candidateSkill->skill->name }} · niveau {{ $candidateSkill->level }}</span>
        @empty
            <span class="text-secondary">Aucune compétence.</span>
        @endforelse
    </div>

    <h3 class="h6 fw-bold text-uppercase text-primary">Expérience professionnelle</h3>
    @forelse ($profile->experiences as $experience)
        <div class="mb-3">
            <div class="d-flex justify-content-between">
                <strong>{{ $experience->position }} — {{ $experience->company }}</strong>
                <span class="small text-secondary">{{ $experience->start_date->format('m/Y') }} – {{ $experience->end_date?->format('m/Y') ?? 'présent' }}</span>
            </div>
            @if ($experience->description)
                <p class="small text-secondary mb-0">{{ $experience->description }}</p>
            @endif
        </div>
    @empty
        <p class="text-secondary">Aucune expérience.</p>
    @endforelse

    <h3 class="h6 fw-bold text-uppercase text-primary mt-4">Formation</h3>
    @forelse ($profile->educations as $education)
        <div class="mb-3">
            <div class="d-flex justify-content-between">
                <strong>{{ $education->degree }}{{ $education->field ? ' — '.$education->field : '' }}</strong>
                <span class="small text-secondary">{{ $education->start_date->format('m/Y') }} – {{ $education->end_date?->format('m/Y') ?? 'présent' }}</span>
            </div>
            <p class="small text-secondary mb-0">{{ $education->school }}</p>
        </div>
    @empty
        <p class="text-secondary">Aucune formation.</p>
    @endforelse

    @if (is_array($profile->sectors) && count($profile->sectors))
        <h3 class="h6 fw-bold text-uppercase text-primary mt-4">Secteurs recherchés</h3>
        <p>{{ implode(', ', $profile->sectors) }}</p>
    @endif
</section>
