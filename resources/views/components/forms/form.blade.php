@props([
    'action' => '',
    'method' => 'POST',
    'id' => null,
    'class' => '',
    'enctype' => null,
    'novalidate' => false,
])

<?php
$formId = $id ?? 'form-' . uniqid();
$formClass = 'needs-validation ' . $class;
$formMethod = strtoupper($method);
$csrf = $method !== 'GET' ? csrf_field() : '';
?>

<form
    id="{{ $formId }}"
    action="{{ $action }}"
    method="{{ $formMethod }}"
    class="{{ $formClass }}"
    @if($enctype) enctype="{{ $enctype }}" @endif
    @if($novalidate) novalidate @endif
>
    {{ $csrf }}
    {{ $slot }}
</form>

@push('scripts')
<script>
    // Validation Bootstrap 5 pour tous les formulaires
    (function () {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>
@endpush