<x-app-layout>
    <div class="container py-4" style="max-width: 1100px;">
        <header class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <p class="text-uppercase small fw-semibold text-primary mb-1">Espace candidat</p>
                <h1 class="h2 fw-bold mb-1">Bonjour {{ auth()->user()->name }}</h1>
                <p class="text-secondary mb-0">Voici le résumé de votre profil.</p>
            </div>
            <a href="{{ route('candidate.profile.edit') }}" class="btn btn-primary px-4 py-2">Compléter mon profil</a>
        </header>

        <ul class="nav nav-pills mb-4" role="tablist">
            <li class="nav-item"><a class="nav-link {{ $view === 'resume' ? 'active' : '' }}" href="{{ route('candidate.dashboard', ['view' => 'resume']) }}">Résumé</a></li>
            <li class="nav-item"><a class="nav-link {{ $view === 'cv' ? 'active' : '' }}" href="{{ route('candidate.dashboard', ['view' => 'cv']) }}">Vue CV</a></li>
        </ul>

        @if ($view === 'cv' && $profile)
            <section class="bg-white border rounded-3 p-4 p-md-5 mb-4">
                <div class="text-center border-bottom pb-3 mb-4">
                    @if (auth()->user()->photo_path)
                        <img src="{{ asset('storage/'.auth()->user()->photo_path) }}" alt="Photo de {{ auth()->user()->name }}" class="rounded-circle mb-2" style="width: 96px; height: 96px; object-fit: cover;">
                    @endif
                    <h2 class="h3 fw-bold mb-1">{{ auth()->user()->name }}</h2>
                    <p class="text-secondary mb-1">{{ $profile->desired_jobs ?: 'Candidat' }} · {{ ucfirst($profile->experience_level) }}</p>
                    <p class="small text-secondary mb-0">
                        {{ $profile->city }}{{ $profile->region ? ', '.$profile->region : '' }} · {{ auth()->user()->email }}{{ auth()->user()->phone ? ' · '.auth()->user()->phone : '' }}
                    </p>
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
        @elseif (! $profile)
            <div class="alert alert-info">Vous n'avez pas encore de profil. <a href="{{ route('candidate.profile.edit') }}">Créez-le maintenant</a>.</div>
        @else
            <section class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="bg-white border rounded-3 p-4 h-100">
                        <h2 class="h6 fw-bold">Localisation</h2>
                        <p class="mb-1">{{ $profile->city ?: 'Non renseignée' }}{{ $profile->region ? ', '.$profile->region : '' }}</p>
                        <p class="small text-secondary mb-0">Mobilité : {{ $profile->mobility }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white border rounded-3 p-4 h-100">
                        <h2 class="h6 fw-bold">Préférences</h2>
                        <p class="mb-1">{{ strtoupper($profile->contract_type ?: '') ?: 'Contrat non défini' }}</p>
                        <p class="small text-secondary mb-0">Salaire : {{ $profile->desired_salary ? number_format($profile->desired_salary, 0, ',', ' ') : 'Non défini' }} · Alertes : {{ $profile->alerts_enabled ? 'activées' : 'désactivées' }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white border rounded-3 p-4 h-100">
                        <h2 class="h6 fw-bold">Niveau</h2>
                        <p class="mb-1 text-capitalize">{{ $profile->experience_level }}</p>
                        <p class="small text-secondary mb-0">{{ $profile->education_level ?: 'Études non renseignées' }}</p>
                    </div>
                </div>
            </section>

            <section class="bg-white border rounded-3 p-4 mb-4">
                <h2 class="h5 fw-bold mb-3">Compétences ({{ $profile->candidateSkills->count() }})</h2>
                <div class="d-flex flex-wrap gap-2">
                    @forelse ($profile->candidateSkills as $candidateSkill)
                        <span class="badge text-bg-light border text-dark">{{ $candidateSkill->skill->name }} · niveau {{ $candidateSkill->level }}</span>
                    @empty
                        <span class="text-secondary">Aucune compétence ajoutée.</span>
                    @endforelse
                </div>
            </section>

            <section class="bg-white border rounded-3 p-4 mb-4">
                <h2 class="h5 fw-bold mb-3">Expériences ({{ $profile->experiences->count() }})</h2>
                <ul class="mb-0">
                    @forelse ($profile->experiences as $experience)
                        <li>{{ $experience->position }} — {{ $experience->company }} ({{ $experience->start_date->format('Y-m') }} → {{ $experience->end_date?->format('Y-m') ?? 'auj.' }})</li>
                    @empty
                        <li class="text-secondary">Aucune expérience.</li>
                    @endforelse
                </ul>
            </section>

            <section class="bg-white border rounded-3 p-4 mb-4">
                <h2 class="h5 fw-bold mb-3">Formations ({{ $profile->educations->count() }})</h2>
                <ul class="mb-0">
                    @forelse ($profile->educations as $education)
                        <li>{{ $education->degree }} — {{ $education->school }} ({{ $education->start_date->format('Y-m') }} → {{ $education->end_date?->format('Y-m') ?? 'auj.' }})</li>
                    @empty
                        <li class="text-secondary">Aucune formation.</li>
                    @endforelse
                </ul>
            </section>
        @endif
    </div>
</x-app-layout>
