{{-- Marque Job2You : icône + nom. Taille réglable via la variable "$size". --}}
@php($size = $size ?? 'md')

@if($size === 'sm')
    <img src="{{ asset('logo.png') }}" alt="Job2You" style="height: 1.75rem; width: 1.75rem; border-radius: 0.375rem; object-fit: cover; object-position: center 46%;">
    <span class="fs-5 fw-semibold text-primary">Job2You</span>
@elseif($size === 'lg')
    <img src="{{ asset('logo.png') }}" alt="Job2You" style="height: 2.5rem; width: auto; border-radius: 0.5rem; object-fit: cover; object-position: center 46%;">
@else
    <img src="{{ asset('logo.png') }}" alt="Job2You" style="height: 2.25rem; width: 2.25rem; border-radius: 0.5rem; object-fit: cover; object-position: center 46%;">
    <span class="fs-5 fw-semibold text-primary">Job2You</span>
@endif
