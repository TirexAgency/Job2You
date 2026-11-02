@props([
    'name' => '',
    'label' => '',
    'type' => 'text',
    'value' => null,
    'placeholder' => '',
    'required' => false,
    'error' => null,
    'help' => null,
    'id' => null,
    'class' => '',
    'autocomplete' => 'off',
    'min' => null,
    'max' => null,
    'step' => null,
    'rows' => null,
    'options' => [],
    'multiple' => false,
    'disabled' => false,
    'readonly' => false,
])

<?php
$id = $id ?? $name;
$fieldClass = 'form-control ' . ($error ? 'is-invalid' : '') . ' ' . $class;
$wrapperClass = 'mb-3';
?>

<div class="{{ $wrapperClass }}">
    @if($label)
        <label for="{{ $id }}" class="form-label fw-medium">
            {{ $label }}
            @if($required)
                <span class="text-danger" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    @if($type === 'textarea')
        <textarea
            id="{{ $id }}"
            name="{{ $name }}"
            class="{{ $fieldClass }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            @if($rows) rows="{{ $rows }}" @endif
        >{{ old($name, $value) }}</textarea>
    @elseif($type === 'select')
        <select
            id="{{ $id }}"
            name="{{ $name }}{{ $multiple ? '[]' : '' }}"
            class="form-select {{ $error ? 'is-invalid' : '' }} {{ $class }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($multiple) multiple @endif
        >
            @if(!$required && !$multiple)
                <option value="" disabled selected>{{ $placeholder ?? 'Sélectionnez...' }}</option>
            @endif
            @foreach($options as $key => $option)
                <option value="{{ $key }}" {{ (old($name, $value) == $key) ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>
    @elseif($type === 'checkbox')
        <div class="form-check">
            <input
                type="checkbox"
                id="{{ $id }}"
                name="{{ $name }}"
                class="form-check-input {{ $error ? 'is-invalid' : '' }}"
                value="1"
                @if(old($name, $value)) checked @endif
                @if($disabled) disabled @endif
                @if($readonly) readonly @endif
            >
            <label for="{{ $id }}" class="form-check-label">{{ $label }}</label>
        </div>
    @elseif($type === 'radio')
        <div class="form-check">
            <input
                type="radio"
                id="{{ $id }}"
                name="{{ $name }}"
                class="form-check-input {{ $error ? 'is-invalid' : '' }}"
                value="{{ $value }}"
                @if(old($name) == $value) checked @endif
                @if($disabled) disabled @endif
                @if($readonly) readonly @endif
            >
            <label for="{{ $id }}" class="form-check-label">{{ $label }}</label>
        </div>
    @elseif($type === 'file')
        <input
            type="file"
            id="{{ $id }}"
            name="{{ $name }}"
            class="form-control {{ $error ? 'is-invalid' : '' }} {{ $class }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($multiple) multiple @endif
        >
    @else
        <input
            type="{{ $type }}"
            id="{{ $id }}"
            name="{{ $name }}"
            class="{{ $fieldClass }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            @if($min !== null) min="{{ $min }}" @endif
            @if($max !== null) max="{{ $max }}" @endif
            @if($step !== null) step="{{ $step }}" @endif
            autocomplete="{{ $autocomplete }}"
        >
    @endif

    @if($error)
        <div class="invalid-feedback d-block">{{ $error }}</div>
    @endif

    @if($help && !$error)
        <div class="form-text text-muted">{{ $help }}</div>
    @endif
</div>