<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('patch')

    <div class="mb-3">
        <label for="photo" class="form-label">Photo de profil</label>
        <label for="photo" class="photo-dropzone @error('photo') is-invalid @enderror" data-photo-dropzone>
            <input id="photo" name="photo" type="file" class="visually-hidden" accept="image/*" data-photo-input>
            <span class="photo-dropzone-preview" data-photo-preview aria-hidden="true">
                @if ($user->photo_url)
                    <img src="{{ $user->photo_url }}" alt="">
                @else
                    <i class="bi bi-camera"></i>
                @endif
            </span>
            <span class="photo-dropzone-text">
                <strong>Glissez-déposez votre photo</strong>
                <span class="d-block small text-secondary">ou cliquez pour parcourir (JPG, PNG — 2 Mo max.)</span>
            </span>
        </label>
        @error('photo')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="name" class="form-label">Nom complet</label>
        <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="phone" class="form-label">Numéro de téléphone</label>
        <input id="phone" name="phone" type="tel" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" autocomplete="tel">
        @error('phone')
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

@push('scripts')
    <script>
        (function () {
            document.querySelectorAll('[data-photo-dropzone]').forEach(function (zone) {
                const input = zone.querySelector('[data-photo-input]');
                const preview = zone.querySelector('[data-photo-preview]');

                if (!input || !preview) {
                    return;
                }

                function showPreview(file) {
                    if (!file || !file.type || !file.type.startsWith('image/')) {
                        return;
                    }

                    const reader = new FileReader();

                    reader.onload = function (event) {
                        preview.innerHTML = '';
                        const image = document.createElement('img');
                        image.src = event.target.result;
                        image.alt = '';
                        preview.appendChild(image);
                    };

                    reader.readAsDataURL(file);
                }

                ['dragenter', 'dragover'].forEach(function (eventName) {
                    zone.addEventListener(eventName, function (event) {
                        event.preventDefault();
                        zone.classList.add('is-dragover');
                    });
                });

                ['dragleave', 'drop'].forEach(function (eventName) {
                    zone.addEventListener(eventName, function (event) {
                        event.preventDefault();
                        zone.classList.remove('is-dragover');
                    });
                });

                zone.addEventListener('drop', function (event) {
                    const files = event.dataTransfer && event.dataTransfer.files;

                    if (!files || files.length === 0) {
                        return;
                    }

                    const transfer = new DataTransfer();
                    transfer.items.add(files[0]);
                    input.files = transfer.files;
                    showPreview(files[0]);
                });

                input.addEventListener('change', function () {
                    showPreview(input.files && input.files[0]);
                });
            });
        })();
    </script>
@endpush
