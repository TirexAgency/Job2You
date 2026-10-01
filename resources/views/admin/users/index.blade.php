<x-app-layout>
    <div class="container-fluid px-3 px-lg-5 py-4">
        <div class="mx-auto" style="max-width: 1200px;">
            <header class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <p class="text-uppercase small fw-semibold text-primary mb-1">Administration</p>
                    <h1 class="h2 fw-bold mb-1">Gestion des utilisateurs</h1>
                    <p class="text-secondary mb-0">Gérez les comptes utilisateurs de la plateforme.</p>
                </div>
                <button type="button" class="btn btn-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#createUserModal" data-auto-open="{{ $errors->any() && !old('_user_id') ? 'true' : 'false' }}">
                    <i class="bi bi-plus-lg me-2" aria-hidden="true"></i>Nouvel utilisateur
                </button>
            </header>

            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2" aria-hidden="true"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2" aria-hidden="true"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Filters -->
            <section class="bg-white border rounded-3 shadow-sm p-4 mb-4">
                <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Rechercher</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Nom, email ou téléphone">
                    </div>
                    <div class="col-md-3">
                        <label for="role" class="form-label">Rôle</label>
                        <select class="form-select" id="role" name="role">
                            <option value="">Tous les rôles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ request('role') === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tous les statuts</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary w-100">Filtrer</button>
                    </div>
                </form>
            </section>

            <!-- Users Table -->
            <section class="bg-white border rounded-3 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Utilisateur</th>
                                <th>Contact</th>
                                <th>Rôle</th>
                                <th>Statut</th>
                                <th>SMS restants / quota</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <span class="fw-semibold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $user->name }}</div>
                                                <div class="small text-secondary">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">{{ $user->phone ?: 'Non renseigné' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'recruiter' ? 'warning' : 'info') }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small">{{ $user->getSmsRemaining() }} / {{ $user->getSmsQuotaLimit() }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" title="Voir" data-bs-toggle="modal" data-bs-target="#showUserModal{{ $user->id }}">
                                                <i class="bi bi-eye" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" title="Modifier" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}" data-auto-open="{{ $errors->any() && old('_user_id') == $user->id ? 'true' : 'false' }}">
                                                <i class="bi bi-pencil" aria-hidden="true"></i>
                                            </button>
                                            @if($user->id !== auth()->id())
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                        <i class="bi bi-trash" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-secondary">
                                        <i class="bi bi-inbox fs-1 d-block mb-2" aria-hidden="true"></i>
                                        Aucun utilisateur trouvé
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($users->hasPages())
                    <div class="border-top p-3">
                        {{ $users->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>

    <!-- Create User Modal -->
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h5 fw-bold" id="createUserModalLabel">Nouvel utilisateur</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nom complet</label>
                                <input type="text" class="form-control @error('name') is-invalid @endif" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Adresse email</label>
                                <input type="email" class="form-control @error('email') is-invalid @endif" id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @endif" id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="role" class="form-label">Rôle</label>
                                <select class="form-select @error('role') is-invalid @endif" id="role" name="role" required>
                                    <option value="">Sélectionner un rôle</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label">Statut</label>
                                <select class="form-select @error('status') is-invalid @endif" id="status" name="status" required>
                                    <option value="">Sélectionner un statut</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ old('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="plan" class="form-label">Formule</label>
                                <input type="text" class="form-control @error('plan') is-invalid @endif" id="plan" name="plan" value="{{ old('plan', 'free') }}" required>
                                @error('plan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="sms_quota" class="form-label">Quota SMS</label>
                                <input type="number" class="form-control @error('sms_quota') is-invalid @endif" id="sms_quota" name="sms_quota" value="{{ old('sms_quota', 2) }}" min="0" required>
                                @error('sms_quota')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control @error('password') is-invalid @endif" id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Créer l'utilisateur</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modals -->
    @foreach($users as $user)
        <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModal{{ $user->id }}Label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5 fw-bold" id="editUserModal{{ $user->id }}Label">Modifier {{ $user->name }}</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_user_id" value="{{ $user->id }}">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="edit_name_{{ $user->id }}" class="form-label">Nom complet</label>
                                    <input type="text" class="form-control" id="edit_name_{{ $user->id }}" name="name" value="{{ old('name', $user->name) }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_email_{{ $user->id }}" class="form-label">Adresse email</label>
                                    <input type="email" class="form-control" id="edit_email_{{ $user->id }}" name="email" value="{{ old('email', $user->email) }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_phone_{{ $user->id }}" class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control" id="edit_phone_{{ $user->id }}" name="phone" value="{{ old('phone', $user->phone) }}">
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_role_{{ $user->id }}" class="form-label">Rôle</label>
                                    <select class="form-select" id="edit_role_{{ $user->id }}" name="role" required>
                                        @foreach($roles as $role)
                                            <option value="{{ $role }}" {{ old('role', $user->role) === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_status_{{ $user->id }}" class="form-label">Statut</label>
                                    <select class="form-select" id="edit_status_{{ $user->id }}" name="status" required>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}" {{ old('status', $user->status) === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_plan_{{ $user->id }}" class="form-label">Formule</label>
                                    <input type="text" class="form-control" id="edit_plan_{{ $user->id }}" name="plan" value="{{ old('plan', $user->plan) }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_sms_quota_{{ $user->id }}" class="form-label">Quota SMS</label>
                                    <input type="number" class="form-control" id="edit_sms_quota_{{ $user->id }}" name="sms_quota" value="{{ old('sms_quota', $user->sms_quota) }}" min="0" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_password_{{ $user->id }}" class="form-label">Nouveau mot de passe</label>
                                    <input type="password" class="form-control" id="edit_password_{{ $user->id }}" name="password">
                                    <div class="form-text">Laissez vide pour conserver le mot de passe actuel.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_password_confirmation_{{ $user->id }}" class="form-label">Confirmer le mot de passe</label>
                                    <input type="password" class="form-control" id="edit_password_confirmation_{{ $user->id }}" name="password_confirmation">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Show User Modals -->
    @foreach($users as $user)
        <div class="modal fade" id="showUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="showUserModal{{ $user->id }}Label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5 fw-bold" id="showUserModal{{ $user->id }}Label">{{ $user->name }}</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h3 class="h6 fw-bold mb-3">Informations personnelles</h3>
                                <dl class="row mb-0">
                                    <dt class="col-sm-5 text-secondary">Nom complet</dt>
                                    <dd class="col-sm-7 fw-semibold">{{ $user->name }}</dd>

                                    <dt class="col-sm-5 text-secondary">Adresse email</dt>
                                    <dd class="col-sm-7">{{ $user->email }}</dd>

                                    <dt class="col-sm-5 text-secondary">Téléphone</dt>
                                    <dd class="col-sm-7">{{ $user->phone ?: 'Non renseigné' }}</dd>

                                    <dt class="col-sm-5 text-secondary">Rôle</dt>
                                    <dd class="col-sm-7">
                                        <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'recruiter' ? 'warning' : 'info') }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </dd>

                                    <dt class="col-sm-5 text-secondary">Statut</dt>
                                    <dd class="col-sm-7">
                                        <span class="badge bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </dd>

                                    <dt class="col-sm-5 text-secondary">Email vérifié</dt>
                                    <dd class="col-sm-7">
                                        @if($user->email_verified_at)
                                            <span class="text-success"><i class="bi bi-check-circle me-1" aria-hidden="true"></i>Oui, le {{ $user->email_verified_at->format('d/m/Y H:i') }}</span>
                                        @else
                                            <span class="text-warning"><i class="bi bi-exclamation-circle me-1" aria-hidden="true"></i>Non vérifié</span>
                                        @endif
                                    </dd>
                                </dl>
                            </div>

                            <div class="col-md-6">
                                <h3 class="h6 fw-bold mb-3">Abonnement & SMS</h3>
                                <dl class="row mb-0">
                                    <dt class="col-sm-5 text-secondary">Formule</dt>
                                    <dd class="col-sm-7 fw-semibold">{{ ucfirst($user->plan) }}</dd>

                                    <dt class="col-sm-5 text-secondary">Quota SMS</dt>
                                    <dd class="col-sm-7">{{ $user->getSmsQuotaLimit() }}</dd>

                                    <dt class="col-sm-5 text-secondary">SMS envoyés</dt>
                                    <dd class="col-sm-7">{{ $user->sms_sent }}</dd>

                                    <dt class="col-sm-5 text-secondary">SMS restants</dt>
                                    <dd class="col-sm-7 fw-semibold text-primary">{{ $user->getSmsRemaining() }}</dd>
                                </dl>

                                <h3 class="h6 fw-bold mb-3 mt-4">Informations système</h3>
                                <dl class="row mb-0">
                                    <dt class="col-sm-5 text-secondary">ID utilisateur</dt>
                                    <dd class="col-sm-7">{{ $user->id }}</dd>

                                    <dt class="col-sm-5 text-secondary">Créé le</dt>
                                    <dd class="col-sm-7">{{ $user->created_at->format('d/m/Y H:i') }}</dd>

                                    <dt class="col-sm-5 text-secondary">Dernière mise à jour</dt>
                                    <dd class="col-sm-7">{{ $user->updated_at->format('d/m/Y H:i') }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">Modifier</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelector('[data-auto-open="true"]')?.click();
            });
        </script>
    @endif
</x-app-layout>
