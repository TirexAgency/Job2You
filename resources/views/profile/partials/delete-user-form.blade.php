<form method="post" action="{{ route('profile.destroy') }}" onsubmit="return confirm('La suppression de votre compte est définitive. Continuer ?')">
    @csrf
    @method('delete')

    <div class="row align-items-end g-3">
        <div class="col-md-7">
            <label for="delete_account_password" class="form-label">Mot de passe actuel</label>
            <input id="delete_account_password" name="password" type="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" autocomplete="current-password" required>
            @error('password', 'userDeletion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-5">
            <button type="submit" class="btn btn-outline-danger w-100">Supprimer mon compte</button>
        </div>
    </div>
</form>
