@php($deleteAccountModalId = 'deleteAccountConfirmModal')

<button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#{{ $deleteAccountModalId }}" data-auto-open="{{ $errors->userDeletion->any() ? 'true' : 'false' }}">
    Supprimer mon compte
</button>

<x-confirm-modal
    :id="$deleteAccountModalId"
    title="Supprimer mon compte"
    :action="route('profile.destroy')"
    method="DELETE"
    confirm-label="Supprimer définitivement"
>
    <p class="mb-3">Cette action est définitive. Saisissez votre mot de passe pour confirmer.</p>
    <label for="delete_account_password" class="form-label">Mot de passe actuel</label>
    <input id="delete_account_password" name="password" type="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" autocomplete="current-password" required>
    @error('password', 'userDeletion')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</x-confirm-modal>

@if ($errors->userDeletion->any())
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelector('[data-auto-open="true"]')?.click();
            });
        </script>
    @endpush
@endif
