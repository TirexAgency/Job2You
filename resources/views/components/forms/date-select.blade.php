@props([
    'name',
    'id' => null,
    'value' => null,
    'required' => false,
    'maxYear' => null,
])

@php
    $id = $id ?? $name;
    $selected = old($name, $value);
    $date = $selected ? \Illuminate\Support\Carbon::parse($selected) : null;

    $day = old($name.'_day', $date?->day);
    $month = old($name.'_month', $date?->month);
    $year = old($name.'_year', $date?->year);

    $months = [
        1 => 'Janvier',
        2 => 'Février',
        3 => 'Mars',
        4 => 'Avril',
        5 => 'Mai',
        6 => 'Juin',
        7 => 'Juillet',
        8 => 'Août',
        9 => 'Septembre',
        10 => 'Octobre',
        11 => 'Novembre',
        12 => 'Décembre',
    ];

    $maxYear = (int) ($maxYear ?? now()->year + 5);
    $minYear = 1970;
    $hasError = $errors->has($name);
@endphp

<div class="date-select-group @if ($hasError) is-invalid @endif">
    <select
        id="{{ $id }}"
        name="{{ $name }}_day"
        class="form-select date-select"
        aria-label="Jour"
        @required($required)
    >
        <option value="">Jour</option>
        @for ($dayOption = 1; $dayOption <= 31; $dayOption++)
            <option value="{{ $dayOption }}" @selected((string) $day === (string) $dayOption)>{{ $dayOption }}</option>
        @endfor
    </select>

    <select
        id="{{ $id }}_month"
        name="{{ $name }}_month"
        class="form-select date-select"
        aria-label="Mois"
        @required($required)
    >
        <option value="">Mois</option>
        @foreach ($months as $monthValue => $monthLabel)
            <option value="{{ $monthValue }}" @selected((string) $month === (string) $monthValue)>{{ $monthLabel }}</option>
        @endforeach
    </select>

    <select
        id="{{ $id }}_year"
        name="{{ $name }}_year"
        class="form-select date-select"
        aria-label="Année"
        @required($required)
    >
        <option value="">Année</option>
        @for ($yearOption = $maxYear; $yearOption >= $minYear; $yearOption--)
            <option value="{{ $yearOption }}" @selected((string) $year === (string) $yearOption)>{{ $yearOption }}</option>
        @endfor
    </select>
</div>

@if ($hasError)
    <div class="invalid-feedback d-block">{{ $errors->first($name) }}</div>
@endif
