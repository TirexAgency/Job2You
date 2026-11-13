<x-app-layout>
    <div class="app-container candidate-profile">
        <header class="app-page-header">
            <div>
                <p class="app-eyebrow">Espace candidat</p>
                <h1 class="h2 fw-bold mb-1">Mon profil</h1>
                <p class="text-secondary mb-0">Complétez vos informations pour recevoir des recommandations toujours plus pertinentes.</p>
            </div>
            <button class="btn btn-outline-primary align-self-start align-self-md-center" type="button" data-bs-toggle="modal" data-bs-target="#cvPreviewModal">
                <i class="bi bi-eye me-1" aria-hidden="true"></i>Aperçu de mon CV
            </button>
        </header>

        <div class="modal fade" id="cvPreviewModal" tabindex="-1" aria-labelledby="cvPreviewLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title fs-5" id="cvPreviewLabel">Aperçu de votre CV</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        @include('candidate._cv', ['profile' => $profile])
                    </div>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-info-button" data-bs-toggle="tab" data-bs-target="#t-info" type="button" role="tab" aria-controls="t-info" aria-selected="true">
                    <i class="bi bi-person-vcard" aria-hidden="true"></i>Informations
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-skills-button" data-bs-toggle="tab" data-bs-target="#t-skills" type="button" role="tab" aria-controls="t-skills" aria-selected="false">
                    <i class="bi bi-lightning-charge" aria-hidden="true"></i>Compétences
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-exp-button" data-bs-toggle="tab" data-bs-target="#t-exp" type="button" role="tab" aria-controls="t-exp" aria-selected="false">
                    <i class="bi bi-briefcase" aria-hidden="true"></i>Expériences
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-edu-button" data-bs-toggle="tab" data-bs-target="#t-edu" type="button" role="tab" aria-controls="t-edu" aria-selected="false">
                    <i class="bi bi-mortarboard" aria-hidden="true"></i>Formations
                </button>
            </li>
        </ul>

        <div class="tab-content">
            {{-- Informations & préférences --}}
            <div class="tab-pane fade show active" id="t-info" role="tabpanel" aria-labelledby="tab-info-button">
                <section class="app-card mb-4">
                    <div class="app-card-header">
                        <div>
                            <h2 class="app-section-title">Informations & localisation</h2>
                            <p class="small text-secondary mb-0">Les champs marqués d’un astérisque sont obligatoires.</p>
                        </div>
                        <span class="badge bg-primary-subtle text-primary-emphasis">Profil candidat</span>
                    </div>

                    <div class="app-card-body">
                        <form method="POST" action="{{ route('candidate.profile.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="desired_jobs" class="form-label">Poste recherché</label>
                                    <input type="text" id="desired_jobs" name="desired_jobs" class="form-control @error('desired_jobs') is-invalid @enderror" value="{{ old('desired_jobs', $profile->desired_jobs) }}" placeholder="Ex. : Développeur PHP">
                                    @error('desired_jobs')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="experience_level" class="form-label">Niveau d’expérience <span class="text-danger" aria-hidden="true">*</span></label>
                                    <select id="experience_level" name="experience_level" class="form-select @error('experience_level') is-invalid @enderror" required>
                                        @foreach (['junior' => 'Junior', 'mid' => 'Intermédiaire', 'senior' => 'Senior'] as $value => $label)
                                            <option value="{{ $value }}" @selected(old('experience_level', $profile->experience_level) === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('experience_level')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="city" class="form-label">Ville <span class="text-danger" aria-hidden="true">*</span></label>
                                    <input type="text" id="city" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $profile->city) }}" placeholder="Ex. : Antananarivo" required>
                                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="region" class="form-label">Région</label>
                                    <input type="text" id="region" name="region" class="form-control @error('region') is-invalid @enderror" value="{{ old('region', $profile->region) }}" placeholder="Ex. : Analamanga">
                                    @error('region')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="mobility" class="form-label">Mobilité <span class="text-danger" aria-hidden="true">*</span></label>
                                    <select id="mobility" name="mobility" class="form-select @error('mobility') is-invalid @enderror" required>
                                        @foreach (['local' => 'Locale', 'regional' => 'Régionale', 'national' => 'Nationale', 'remote' => 'Télétravail', 'international' => 'Internationale'] as $value => $label)
                                            <option value="{{ $value }}" @selected(old('mobility', $profile->mobility) === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('mobility')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="number" step="any" id="latitude" name="latitude" class="form-control @error('latitude') is-invalid @enderror" value="{{ old('latitude', $profile->latitude) }}" placeholder="-18.8792">
                                    @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="number" step="any" id="longitude" name="longitude" class="form-control @error('longitude') is-invalid @enderror" value="{{ old('longitude', $profile->longitude) }}" placeholder="47.5079">
                                    @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="education_level" class="form-label">Niveau d’études</label>
                                    <input type="text" id="education_level" name="education_level" class="form-control @error('education_level') is-invalid @enderror" value="{{ old('education_level', $profile->education_level) }}" placeholder="Ex. : Master, Licence…">
                                    @error('education_level')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <h3 class="h6 fw-bold mb-1">Préférences de recherche</h3>
                            <p class="small text-secondary mb-3">Ces critères alimentent le calcul de compatibilité et vos alertes SMS.</p>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="contract_type" class="form-label">Type de contrat</label>
                                    <select id="contract_type" name="contract_type" class="form-select @error('contract_type') is-invalid @enderror">
                                        <option value="">Indifférent</option>
                                        @foreach (['cdi' => 'CDI', 'cdd' => 'CDD', 'freelance' => 'Freelance', 'stage' => 'Stage', 'alternance' => 'Alternance', 'interim' => 'Intérim'] as $value => $label)
                                            <option value="{{ $value }}" @selected(old('contract_type', $profile->contract_type) === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('contract_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="desired_salary" class="form-label">Salaire souhaité (Ar)</label>
                                    <input type="number" id="desired_salary" name="desired_salary" class="form-control @error('desired_salary') is-invalid @enderror" value="{{ old('desired_salary', $profile->desired_salary) }}" placeholder="Ex. : 2 500 000" min="0">
                                    @error('desired_salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-4">
                                    <span class="form-label d-block">Alertes SMS</span>
                                    <div class="profile-switch-field">
                                        <div class="form-check form-switch mb-0">
                                            <input type="hidden" name="alerts_enabled" value="0">
                                            <input type="checkbox" name="alerts_enabled" value="1" class="form-check-input" id="alerts_enabled" role="switch" @checked(old('alerts_enabled', $profile->alerts_enabled))>
                                            <label class="form-check-label" for="alerts_enabled">Activer les alertes SMS</label>
                                        </div>
                                    </div>
                                    <div class="form-text">Recevez un SMS lorsqu’une offre correspond à votre profil.</div>
                                </div>

                                <div class="col-12">
                                    <label for="sectors_text" class="form-label">Secteurs recherchés</label>
                                    <textarea id="sectors_text" name="sectors_text" class="form-control @error('sectors_text') is-invalid @enderror" rows="3" placeholder="Un secteur par ligne&#10;Ex. : Informatique&#10;Télécommunications">{{ old('sectors_text', is_array($profile->sectors) ? implode("\n", $profile->sectors) : '') }}</textarea>
                                    <div class="form-text">Un secteur par ligne. Laissez vide si vous n’avez pas de préférence.</div>
                                    @error('sectors_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button class="btn btn-primary px-4">
                                    <i class="bi bi-check-lg me-1" aria-hidden="true"></i>Enregistrer les informations
                                </button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>

            {{-- Compétences --}}
            <div class="tab-pane fade" id="t-skills" role="tabpanel" aria-labelledby="tab-skills-button">
                <section class="app-card mb-4">
                    <div class="app-card-header">
                        <div>
                            <h2 class="app-section-title">Compétences</h2>
                            <p class="small text-secondary mb-0">Ajoutez vos compétences et précisez votre niveau de maîtrise.</p>
                        </div>
                        <span class="badge bg-primary-subtle text-primary-emphasis">{{ $profile->candidateSkills->count() }} compétence(s)</span>
                    </div>

                    <div class="app-card-body">
                        <div class="mb-4">
                            <label for="skill-search" class="form-label">Rechercher une compétence</label>
                            <form method="GET" action="{{ route('candidate.profile.edit') }}" class="skill-search-bar">
                                <input type="hidden" name="tab" value="skills">
                                <input type="text" id="skill-search" name="q" class="form-control" placeholder="Ex. : Laravel, PHP…" value="{{ $skillSearch }}">
                                <button class="btn btn-outline-secondary"><i class="bi bi-search me-1" aria-hidden="true"></i>Rechercher</button>
                                @if ($skillSearch !== '')
                                    <a href="{{ route('candidate.profile.edit', ['tab' => 'skills']) }}" class="btn btn-link skill-search-reset">Réinitialiser</a>
                                @endif
                            </form>
                        </div>

                        <form method="POST" action="{{ route('candidate.skills.store') }}" class="row g-2 align-items-end mb-4">
                            @csrf
                            <div class="col-md-5">
                                <label for="skill_id" class="form-label">Compétence existante</label>
                                <select id="skill_id" name="skill_id" class="form-select">
                                    <option value="">Choisir une compétence…</option>
                                    @forelse ($skills as $skill)
                                        <option value="{{ $skill->id }}">{{ $skill->name }}</option>
                                    @empty
                                        <option value="" disabled>Aucune compétence trouvée</option>
                                    @endforelse
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="skill_name" class="form-label">Ou créer une compétence</label>
                                <input type="text" id="skill_name" name="name" class="form-control" placeholder="Nom de la compétence">
                            </div>
                            <div class="col-md-2">
                                <label for="skill_level" class="form-label">Niveau</label>
                                <select id="skill_level" name="level" class="form-select">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}">Niveau {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Ajouter</button>
                            </div>
                        </form>

                        @if ($skillSearch !== '')
                            <p class="small text-secondary">
                                {{ $skills->count() }} résultat(s) pour « {{ $skillSearch }} ».
                                Vous pouvez aussi créer une nouvelle compétence avec le champ « Ou créer une compétence ».
                            </p>
                        @endif

                        <div class="list-group">
                            @forelse ($profile->candidateSkills as $candidateSkill)
                                <div class="list-group-item d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="app-icon bg-primary-subtle text-primary" aria-hidden="true"><i class="bi bi-code-slash"></i></span>
                                        <span class="fw-semibold">{{ $candidateSkill->skill->name }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <form method="POST" action="{{ route('candidate.skills.update', ['candidateSkill' => $candidateSkill->skill_id]) }}" class="d-flex align-items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <label class="visually-hidden" for="level-{{ $candidateSkill->skill_id }}">Niveau pour {{ $candidateSkill->skill->name }}</label>
                                            <select id="level-{{ $candidateSkill->skill_id }}" name="level" class="form-select form-select-sm" style="width: auto;">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <option value="{{ $i }}" @selected($candidateSkill->level === $i)>Niveau {{ $i }}</option>
                                                @endfor
                                            </select>
                                            <button class="btn btn-sm btn-outline-primary" title="Enregistrer le niveau" aria-label="Enregistrer le niveau de {{ $candidateSkill->skill->name }}">
                                                <i class="bi bi-check-lg" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('candidate.skills.destroy', ['candidateSkill' => $candidateSkill->skill_id]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Supprimer" aria-label="Supprimer {{ $candidateSkill->skill->name }}">
                                                <i class="bi bi-trash" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item text-center text-secondary py-4">
                                    <i class="bi bi-lightning-charge fs-3 d-block mb-2" aria-hidden="true"></i>
                                    Aucune compétence pour le moment.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>
            </div>

            {{-- Expériences --}}
            <div class="tab-pane fade" id="t-exp" role="tabpanel" aria-labelledby="tab-exp-button">
                <section class="app-card mb-4">
                    <div class="app-card-header">
                        <div>
                            <h2 class="app-section-title">Historique professionnel</h2>
                            <p class="small text-secondary mb-0">Ajoutez vos expériences les plus récentes en premier.</p>
                        </div>
                        <span class="badge bg-primary-subtle text-primary-emphasis">{{ $profile->experiences->count() }} expérience(s)</span>
                    </div>

                    <div class="app-card-body">
                        <form method="POST" action="{{ route('candidate.experiences.store') }}" class="row g-3 mb-4">
                            @csrf
                            <div class="col-md-6">
                                <label for="experience_company" class="form-label">Entreprise <span class="text-danger" aria-hidden="true">*</span></label>
                                <input type="text" id="experience_company" name="company" class="form-control" placeholder="Nom de l’entreprise" required>
                            </div>
                            <div class="col-md-6">
                                <label for="experience_position" class="form-label">Poste <span class="text-danger" aria-hidden="true">*</span></label>
                                <input type="text" id="experience_position" name="position" class="form-control" placeholder="Intitulé du poste" required>
                            </div>
                            <div class="col-md-6">
                                <label for="experience_start_date" class="form-label">Date de début <span class="text-danger" aria-hidden="true">*</span></label>
                                <x-forms.date-select name="start_date" id="experience_start_date" :max-year="(int) now()->year" :required="true" />
                            </div>
                            <div class="col-md-6">
                                <label for="experience_end_date" class="form-label">Date de fin</label>
                                <x-forms.date-select name="end_date" id="experience_end_date" />
                                <div class="form-text">Laissez vide si le poste est toujours en cours.</div>
                            </div>
                            <div class="col-12">
                                <label for="experience_description" class="form-label">Description</label>
                                <textarea id="experience_description" name="description" class="form-control" rows="3" placeholder="Missions, réalisations, technologies utilisées…"></textarea>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Ajouter l’expérience</button>
                            </div>
                        </form>

                        <div class="list-group">
                            @forelse ($profile->experiences as $experience)
                                <div class="list-group-item">
                                    <div class="d-flex flex-column flex-sm-row align-items-sm-start justify-content-between gap-3">
                                        <div>
                                            <div class="fw-semibold">{{ $experience->position }} <span class="text-secondary fw-normal">— {{ $experience->company }}</span></div>
                                            <div class="small text-secondary">
                                                <i class="bi bi-calendar3 me-1" aria-hidden="true"></i>
                                                {{ $experience->start_date->format('m/Y') }} → {{ $experience->end_date?->format('m/Y') ?? 'aujourd’hui' }}
                                            </div>
                                            @if ($experience->description)
                                                <p class="small text-secondary mb-0 mt-2">{{ $experience->description }}</p>
                                            @endif
                                        </div>
                                        <div class="d-flex gap-2 flex-shrink-0">
                                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#exp-edit-{{ $experience->id }}" aria-expanded="false" aria-controls="exp-edit-{{ $experience->id }}" title="Modifier">
                                                <i class="bi bi-pencil" aria-hidden="true"></i>
                                            </button>
                                            <form method="POST" action="{{ route('candidate.experiences.destroy', $experience) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="Supprimer" aria-label="Supprimer l’expérience {{ $experience->position }}">
                                                    <i class="bi bi-trash" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="collapse mt-3" id="exp-edit-{{ $experience->id }}">
                                        <form method="POST" action="{{ route('candidate.experiences.update', $experience) }}" class="row g-3 border-top pt-3">
                                            @csrf
                                            @method('PUT')
                                            <div class="col-md-6">
                                                <label for="exp-company-{{ $experience->id }}" class="form-label">Entreprise</label>
                                                <input type="text" id="exp-company-{{ $experience->id }}" name="company" class="form-control" value="{{ $experience->company }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="exp-position-{{ $experience->id }}" class="form-label">Poste</label>
                                                <input type="text" id="exp-position-{{ $experience->id }}" name="position" class="form-control" value="{{ $experience->position }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="exp-start-{{ $experience->id }}" class="form-label">Date de début</label>
                                                <x-forms.date-select name="start_date" id="exp-start-{{ $experience->id }}" :value="$experience->start_date" :max-year="(int) now()->year" :required="true" />
                                            </div>
                                            <div class="col-md-6">
                                                <label for="exp-end-{{ $experience->id }}" class="form-label">Date de fin</label>
                                                <x-forms.date-select name="end_date" id="exp-end-{{ $experience->id }}" :value="$experience->end_date" />
                                            </div>
                                            <div class="col-12">
                                                <label for="exp-description-{{ $experience->id }}" class="form-label">Description</label>
                                                <textarea id="exp-description-{{ $experience->id }}" name="description" class="form-control" rows="3">{{ $experience->description }}</textarea>
                                            </div>
                                            <div class="col-12">
                                                <button class="btn btn-sm btn-primary"><i class="bi bi-check-lg me-1" aria-hidden="true"></i>Enregistrer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item text-center text-secondary py-4">
                                    <i class="bi bi-briefcase fs-3 d-block mb-2" aria-hidden="true"></i>
                                    Aucune expérience pour le moment.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>
            </div>

            {{-- Formations --}}
            <div class="tab-pane fade" id="t-edu" role="tabpanel" aria-labelledby="tab-edu-button">
                <section class="app-card mb-4">
                    <div class="app-card-header">
                        <div>
                            <h2 class="app-section-title">Parcours académique</h2>
                            <p class="small text-secondary mb-0">Ajoutez vos diplômes et formations.</p>
                        </div>
                        <span class="badge bg-primary-subtle text-primary-emphasis">{{ $profile->educations->count() }} formation(s)</span>
                    </div>

                    <div class="app-card-body">
                        <form method="POST" action="{{ route('candidate.educations.store') }}" class="row g-3 mb-4">
                            @csrf
                            <div class="col-md-6">
                                <label for="education_school" class="form-label">Établissement <span class="text-danger" aria-hidden="true">*</span></label>
                                <input type="text" id="education_school" name="school" class="form-control" placeholder="Nom de l’établissement" required>
                            </div>
                            <div class="col-md-6">
                                <label for="education_degree" class="form-label">Diplôme <span class="text-danger" aria-hidden="true">*</span></label>
                                <input type="text" id="education_degree" name="degree" class="form-control" placeholder="Ex. : Licence, Master…" required>
                            </div>
                            <div class="col-md-4">
                                <label for="education_field" class="form-label">Domaine</label>
                                <input type="text" id="education_field" name="field" class="form-control" placeholder="Ex. : Informatique">
                            </div>
                            <div class="col-md-4">
                                <label for="education_start_date" class="form-label">Date de début <span class="text-danger" aria-hidden="true">*</span></label>
                                <x-forms.date-select name="start_date" id="education_start_date" :max-year="(int) now()->year" :required="true" />
                            </div>
                            <div class="col-md-4">
                                <label for="education_end_date" class="form-label">Date de fin</label>
                                <x-forms.date-select name="end_date" id="education_end_date" />
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Ajouter la formation</button>
                            </div>
                        </form>

                        <div class="list-group">
                            @forelse ($profile->educations as $education)
                                <div class="list-group-item">
                                    <div class="d-flex flex-column flex-sm-row align-items-sm-start justify-content-between gap-3">
                                        <div>
                                            <div class="fw-semibold">{{ $education->degree }} <span class="text-secondary fw-normal">— {{ $education->school }}</span></div>
                                            @if ($education->field)
                                                <div class="small text-secondary">{{ $education->field }}</div>
                                            @endif
                                            <div class="small text-secondary">
                                                <i class="bi bi-calendar3 me-1" aria-hidden="true"></i>
                                                {{ $education->start_date->format('m/Y') }} → {{ $education->end_date?->format('m/Y') ?? 'aujourd’hui' }}
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 flex-shrink-0">
                                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#edu-edit-{{ $education->id }}" aria-expanded="false" aria-controls="edu-edit-{{ $education->id }}" title="Modifier">
                                                <i class="bi bi-pencil" aria-hidden="true"></i>
                                            </button>
                                            <form method="POST" action="{{ route('candidate.educations.destroy', $education) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="Supprimer" aria-label="Supprimer la formation {{ $education->degree }}">
                                                    <i class="bi bi-trash" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="collapse mt-3" id="edu-edit-{{ $education->id }}">
                                        <form method="POST" action="{{ route('candidate.educations.update', $education) }}" class="row g-3 border-top pt-3">
                                            @csrf
                                            @method('PUT')
                                            <div class="col-md-6">
                                                <label for="edu-school-{{ $education->id }}" class="form-label">Établissement</label>
                                                <input type="text" id="edu-school-{{ $education->id }}" name="school" class="form-control" value="{{ $education->school }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edu-degree-{{ $education->id }}" class="form-label">Diplôme</label>
                                                <input type="text" id="edu-degree-{{ $education->id }}" name="degree" class="form-control" value="{{ $education->degree }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="edu-field-{{ $education->id }}" class="form-label">Domaine</label>
                                                <input type="text" id="edu-field-{{ $education->id }}" name="field" class="form-control" value="{{ $education->field }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="edu-start-{{ $education->id }}" class="form-label">Date de début</label>
                                                <x-forms.date-select name="start_date" id="edu-start-{{ $education->id }}" :value="$education->start_date" :max-year="(int) now()->year" :required="true" />
                                            </div>
                                            <div class="col-md-4">
                                                <label for="edu-end-{{ $education->id }}" class="form-label">Date de fin</label>
                                                <x-forms.date-select name="end_date" id="edu-end-{{ $education->id }}" :value="$education->end_date" />
                                            </div>
                                            <div class="col-12">
                                                <button class="btn btn-sm btn-primary"><i class="bi bi-check-lg me-1" aria-hidden="true"></i>Enregistrer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item text-center text-secondary py-4">
                                    <i class="bi bi-mortarboard fs-3 d-block mb-2" aria-hidden="true"></i>
                                    Aucune formation pour le moment.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const params = new URLSearchParams(window.location.search);
            const map = {
                '#t-info': '#t-info',
                '#t-skills': '#t-skills',
                '#t-exp': '#t-exp',
                '#t-edu': '#t-edu',
                skills: '#t-skills',
                exp: '#t-exp',
                education: '#t-edu',
                preferences: '#t-info',
            };

            const key = window.location.hash || params.get('tab');
            const target = map[key];

            if (!target) {
                return;
            }

            const activateTab = function () {
                const trigger = document.querySelector('[data-bs-toggle="tab"][data-bs-target="' + target + '"]');

                if (!trigger) {
                    return;
                }

                if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                    new bootstrap.Tab(trigger).show();
                } else {
                    trigger.click();
                }

                if (key === 'preferences') {
                    document.querySelector('#t-info form h3')?.scrollIntoView({ behavior: 'smooth' });
                }
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', activateTab);
            } else {
                activateTab();
            }
        })();
    </script>
</x-app-layout>
