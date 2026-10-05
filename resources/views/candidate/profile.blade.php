<x-app-layout>
    <div class="container py-4" style="max-width: 1100px;">
        <h1 class="h3 fw-bold mb-4">Profil candidat</h1>

        @if (session('status'))
            <div class="alert alert-success">Profil mis à jour.</div>
        @endif

        <ul class="nav nav-tabs mb-3" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#t-info" type="button" role="tab">Informations</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#t-skills" type="button" role="tab">Compétences</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#t-exp" type="button" role="tab">Expériences</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#t-edu" type="button" role="tab">Formations</button></li>
        </ul>

        <div class="tab-content">
        <div class="tab-pane fade show active" id="t-info" role="tabpanel">
        <section class="bg-white border rounded-3 p-4 mb-4">
            <h2 class="h5 fw-bold mb-3">Informations & localisation</h2>
            <form method="POST" action="{{ route('candidate.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Poste recherché</label>
                        <input type="text" name="desired_jobs" class="form-control" value="{{ old('desired_jobs', $profile->desired_jobs) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Niveau d'expérience *</label>
                        <select name="experience_level" class="form-select">
                            @foreach (['junior' => 'Junior', 'mid' => 'Intermédiaire', 'senior' => 'Senior'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('experience_level', $profile->experience_level) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('experience_level')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ville *</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $profile->city) }}">
                        @error('city')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Région</label>
                        <input type="text" name="region" class="form-control" value="{{ old('region', $profile->region) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mobilité *</label>
                        <select name="mobility" class="form-select">
                            @foreach (['local' => 'Locale', 'regional' => 'Régionale', 'national' => 'Nationale', 'remote' => 'Télétravail', 'international' => 'Internationale'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('mobility', $profile->mobility) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('mobility')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Latitude</label>
                        <input type="number" step="any" name="latitude" class="form-control" value="{{ old('latitude', $profile->latitude) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Longitude</label>
                        <input type="number" step="any" name="longitude" class="form-control" value="{{ old('longitude', $profile->longitude) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Niveau d'études</label>
                        <input type="text" name="education_level" class="form-control" value="{{ old('education_level', $profile->education_level) }}">
                    </div>
                </div>

                <h3 class="h6 fw-bold mt-4 mb-3">Préférences de recherche</h3>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Type de contrat</label>
                        <select name="contract_type" class="form-select">
                            <option value="">—</option>
                            @foreach (['cdi' => 'CDI', 'cdd' => 'CDD', 'freelance' => 'Freelance', 'stage' => 'Stage', 'alternance' => 'Alternance', 'interim' => 'Intérim'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('contract_type', $profile->contract_type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Salaire souhaité</label>
                        <input type="number" name="desired_salary" class="form-control" value="{{ old('desired_salary', $profile->desired_salary) }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check">
                            <input type="hidden" name="alerts_enabled" value="0">
                            <input type="checkbox" name="alerts_enabled" value="1" class="form-check-input" id="alerts" @checked(old('alerts_enabled', $profile->alerts_enabled))>
                            <label class="form-check-label" for="alerts">Activer les alertes</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Secteurs (un par ligne)</label>
                        <textarea name="sectors_text" class="form-control" rows="3">{{ old('sectors_text', is_array($profile->sectors) ? implode("\n", $profile->sectors) : '') }}</textarea>
                    </div>
                </div>

                <button class="btn btn-primary mt-3">Enregistrer</button>
            </form>
        </section>
        </div>

        <div class="tab-pane fade" id="t-skills" role="tabpanel">
        <section class="bg-white border rounded-3 p-4 mb-4">
            <h2 class="h5 fw-bold mb-3">Compétences</h2>
            <form method="GET" action="{{ route('candidate.profile.edit') }}" class="row g-2 mb-3">
                <div class="col-md-6">
                    <input type="text" name="q" class="form-control" placeholder="Rechercher une compétence" value="{{ $skillSearch }}">
                </div>
                <div class="col-md-2"><button class="btn btn-outline-secondary">Rechercher</button></div>
            </form>

            <form method="POST" action="{{ route('candidate.skills.store') }}" class="row g-2 mb-3">
                @csrf
                <div class="col-md-5">
                    <select name="skill_id" class="form-select">
                        <option value="">Choisir une compétence…</option>
                        @foreach ($skills as $skill)
                            <option value="{{ $skill->id }}">{{ $skill->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="name" class="form-control" placeholder="Ou créer (nom)">
                </div>
                <div class="col-md-2">
                    <select name="level" class="form-select">
                        @for ($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}">Niveau {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2"><button class="btn btn-primary">Ajouter</button></div>
            </form>

            <ul class="list-group">
                @forelse ($profile->candidateSkills as $candidateSkill)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ $candidateSkill->skill->name }} <span class="badge text-bg-secondary">Niveau {{ $candidateSkill->level }}</span></span>
                        <form method="POST" action="{{ route('candidate.skills.destroy', ['candidateSkill' => $candidateSkill->skill_id]) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                        </form>
                    </li>
                @empty
                    <li class="list-group-item text-secondary">Aucune compétence.</li>
                @endforelse
            </ul>
        </section>
        </div>

        <div class="tab-pane fade" id="t-exp" role="tabpanel">
        <section class="bg-white border rounded-3 p-4 mb-4">
            <h2 class="h5 fw-bold mb-3">Historique professionnel</h2>
            <form method="POST" action="{{ route('candidate.experiences.store') }}" class="row g-2 mb-3">
                @csrf
                <div class="col-md-4"><input type="text" name="company" class="form-control" placeholder="Entreprise" required></div>
                <div class="col-md-4"><input type="text" name="position" class="form-control" placeholder="Poste" required></div>
                <div class="col-md-2"><input type="date" name="start_date" class="form-control" required></div>
                <div class="col-md-2"><input type="date" name="end_date" class="form-control"></div>
                <div class="col-12"><textarea name="description" class="form-control" placeholder="Description"></textarea></div>
                <div class="col-12"><button class="btn btn-primary">Ajouter</button></div>
            </form>
            <ul class="list-group">
                @forelse ($profile->experiences as $experience)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><strong>{{ $experience->position }}</strong> — {{ $experience->company }} ({{ $experience->start_date->format('Y-m') }} → {{ $experience->end_date?->format('Y-m') ?? 'auj.' }})</span>
                        <form method="POST" action="{{ route('candidate.experiences.destroy', $experience) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                        </form>
                    </li>
                @empty
                    <li class="list-group-item text-secondary">Aucune expérience.</li>
                @endforelse
            </ul>
        </section>
        </div>

        <div class="tab-pane fade" id="t-edu" role="tabpanel">
        <section class="bg-white border rounded-3 p-4 mb-4">
            <h2 class="h5 fw-bold mb-3">Parcours académique</h2>
            <form method="POST" action="{{ route('candidate.educations.store') }}" class="row g-2 mb-3">
                @csrf
                <div class="col-md-4"><input type="text" name="school" class="form-control" placeholder="Établissement" required></div>
                <div class="col-md-4"><input type="text" name="degree" class="form-control" placeholder="Diplôme" required></div>
                <div class="col-md-4"><input type="text" name="field" class="form-control" placeholder="Domaine"></div>
                <div class="col-md-3"><input type="date" name="start_date" class="form-control" required></div>
                <div class="col-md-3"><input type="date" name="end_date" class="form-control"></div>
                <div class="col-12"><button class="btn btn-primary">Ajouter</button></div>
            </form>
            <ul class="list-group">
                @forelse ($profile->educations as $education)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><strong>{{ $education->degree }}</strong> — {{ $education->school }} ({{ $education->start_date->format('Y-m') }} → {{ $education->end_date?->format('Y-m') ?? 'auj.' }})</span>
                        <form method="POST" action="{{ route('candidate.educations.destroy', $education) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                        </form>
                    </li>
                @empty
                    <li class="list-group-item text-secondary">Aucune formation.</li>
                @endforelse
            </ul>
        </section>
        </div>
        </div>
    </div>
</x-app-layout>
