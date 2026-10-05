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

        @if (! $profile)
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
