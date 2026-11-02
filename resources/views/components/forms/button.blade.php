@props([
    'type' => 'submit',
    'label' => 'Valider',
    'variant' => 'primary', // primary, secondary, success, danger, warning, info, light, dark, outline-primary, outline-secondary, link
    'size' => 'md', // sm, md, lg
    'icon' => null,
    'icon_position' => 'left', // left, right
    'disabled' => false,
    'loading' => false,
    'class' => '',
    'id' => null,
    'name' => null,
    'value' => null,
    'block' => false, // full width
])

<?php
$variantClasses = [
    'primary' => 'btn-primary',
    'secondary' => 'btn-secondary',
    'success' => 'btn-success',
    'danger' => 'btn-danger',
    'warning' => 'btn-warning',
    'info' => 'btn-info',
    'light' => 'btn-light',
    'dark' => 'btn-dark',
    'outline-primary' => 'btn-outline-primary',
    'outline-secondary' => 'btn-outline-secondary',
    'outline-success' => 'btn-outline-success',
    'outline-danger' => 'btn-outline-danger',
    'outline-warning' => 'btn-outline-warning',
    'outline-info' => 'btn-outline-info',
    'link' => 'btn-link',
];

$sizeClasses = [
    'sm' => 'btn-sm',
    'md' => '',
    'lg' => 'btn-lg',
];

$classes = 'btn ' . ($variantClasses[$variant] ?? 'btn-primary') . ' ' . ($sizeClasses[$size] ?? '') . ' ' . ($block ? 'w-100' : '') . ' ' . $class;
$disabled = $disabled || $loading;
?>

<button
    type="{{ $type }}"
    id="{{ $id }}"
    name="{{ $name }}"
    value="{{ $value }}"
    class="{{ $classes }}"
    @if($disabled) disabled @endif
>
    @if($loading)
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        <span>Chargement...</span>
    @else
        @if($icon && $icon_position === 'left')
            <i class="{{ $icon }} me-1" aria-hidden="true"></i>
        @endif
        <span>{{ $label }}</span>
        @if($icon && $icon_position === 'right')
            <i class="{{ $icon }} ms-1" aria-hidden="true"></i>
        @endif
    @endif
</button>