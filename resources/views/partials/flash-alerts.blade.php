@php
    $statusMessages = [
        'profile-updated' => 'Votre profil a été mis à jour.',
        'alerts-updated' => 'Vos alertes SMS ont été mises à jour.',
        'sms-deleted' => 'La notification a été supprimée.',
        'plan-changed' => 'Votre formule a été mise à jour.',
        'cv-uploaded' => 'Votre CV a été importé. L’analyse est en attente.',
        'cv-validated' => 'Les données du CV ont été validées.',
        'verification-link-sent' => 'Un nouveau lien de vérification a été envoyé à votre adresse e-mail.',
    ];

    $flashMessages = [];

    foreach (['success' => 'success', 'error' => 'danger', 'warning' => 'warning', 'info' => 'info'] as $key => $variant) {
        if (session()->has($key)) {
            $flashMessages[] = [
                'variant' => $variant,
                'message' => session($key),
                'dismissible' => true,
            ];
        }
    }

    if (session()->has('status')) {
        $status = session('status');

        $flashMessages[] = [
            'variant' => 'success',
            'message' => $statusMessages[$status] ?? $status,
            'dismissible' => true,
        ];
    }
@endphp

@if (! empty($flashMessages))
    <div class="flash-alerts">
        @foreach ($flashMessages as $flash)
            @php
                $icon = match ($flash['variant']) {
                    'danger' => 'bi-exclamation-octagon-fill',
                    'warning' => 'bi-exclamation-triangle-fill',
                    'info' => 'bi-info-circle-fill',
                    default => 'bi-check-circle-fill',
                };
            @endphp

            <div class="alert alert-{{ $flash['variant'] }} alert-dismissible fade show d-flex align-items-start gap-2" role="alert" @if ($flash['variant'] !== 'danger') data-auto-dismiss @endif>
                <i class="bi {{ $icon }} alert-icon mt-1" aria-hidden="true"></i>
                <div class="flex-grow-1">{{ $flash['message'] }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        @endforeach
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-start gap-2" role="alert">
        <i class="bi bi-exclamation-octagon-fill alert-icon mt-1" aria-hidden="true"></i>
        <div class="flex-grow-1">
            <p class="fw-semibold mb-1">Merci de corriger les erreurs suivantes :</p>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
@endif
