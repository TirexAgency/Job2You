<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}">
    @csrf
    @method('patch')

    <div class="mb-3">
        <label for="name" class="form-label">Nom complet</label>
        <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-4">
        <label for="email" class="form-label">Adresse email</label>
        <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-2">
                <p class="small text-warning-emphasis mb-1">Cette adresse email n'est pas encore vérifiée.</p>
                <button form="send-verification" class="btn btn-sm btn-link px-0">Renvoyer le lien de vérification</button>
                @if (session('status') === 'verification-link-sent')
                    <p class="small text-success mb-0">Un nouveau lien de vérification a été envoyé.</p>
                @endif
            </div>
        @endif
    </div>

    <button type="submit" class="btn btn-primary">Enregistrer les informations</button>
</form>
