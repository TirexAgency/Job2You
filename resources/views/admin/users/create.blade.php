<x-app-layout>
    <div class="app-container">
        <div class="mx-auto" style="max-width: 800px;">
            <header class="app-page-header">
                <div>
                    <p class="app-eyebrow">Administration</p>
                    <h1 class="h2 fw-bold mb-1">Nouvel utilisateur</h1>
                    <p class="text-secondary mb-0">Créez un nouveau compte utilisateur.</p>
                </div>
            </header>

            <section class="app-card p-4 p-lg-5">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nom complet</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Adresse email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="role" class="form-label">Rôle</label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="">Sélectionner un rôle</option>
                                @foreach($roles as $roleValue => $roleLabel)
                                    <option value="{{ $roleValue }}" {{ old('role') === $roleValue ? 'selected' : '' }}>{{ $roleLabel }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="">Sélectionner un statut</option>
                                @foreach($statuses as $statusValue => $statusLabel)
                                    <option value="{{ $statusValue }}" {{ old('status') === $statusValue ? 'selected' : '' }}>{{ $statusLabel }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="plan" class="form-label">Formule</label>
                            <input type="text" class="form-control @error('plan') is-invalid @enderror" id="plan" name="plan" value="{{ old('plan', 'free') }}" required>
                            @error('plan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="sms_quota" class="form-label">Quota SMS</label>
                            <input type="number" class="form-control @error('sms_quota') is-invalid @enderror" id="sms_quota" name="sms_quota" value="{{ old('sms_quota', 2) }}" min="0" required>
                            @error('sms_quota')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">Créer l'utilisateur</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
