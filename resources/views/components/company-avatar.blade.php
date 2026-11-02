@props(['name' => 'Job2You', 'size' => 'md'])

@php
    $companyName = trim((string) $name) !== '' ? trim((string) $name) : 'Job2You';
    $initial = mb_strtoupper(mb_substr($companyName, 0, 1));
    $palettes = [
        ['#dbeafe', '#1d4ed8'],
        ['#dcfce7', '#15803d'],
        ['#fef3c7', '#b45309'],
        ['#fce7f3', '#be185d'],
        ['#ede9fe', '#6d28d9'],
        ['#cffafe', '#0e7490'],
    ];
    [$background, $color] = $palettes[abs(crc32($companyName)) % count($palettes)];
@endphp

<span
    {{ $attributes->class(['company-avatar', 'company-avatar-sm' => $size === 'sm']) }}
    style="--avatar-bg: {{ $background }}; --avatar-color: {{ $color }};"
    aria-hidden="true"
>{{ $initial }}</span>
