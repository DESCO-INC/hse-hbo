@props([
    'label' => '',
    'name',
    'options' => [],
    'value' => '',
    'size' => 'md',
    'width' => '40',
    'readonly' => false,
])

@php
    $error = $errors->first($name);

    // Size variants
    $sizes = [
        'sm' => ['select' => 'px-2 py-1 text-sm', 'label' => 'text-xs'],
        'md' => ['select' => 'px-2 py-1.5 text-sm', 'label' => 'text-sm'],
        'lg' => ['select' => 'px-3 py-2 text-base', 'label' => 'text-base'],
    ];

    $selectSize = $sizes[$size]['select'] ?? $sizes['md']['select'];
    $labelSize = $sizes[$size]['label'] ?? $sizes['md']['label'];

    // Width class
    $widthClass = $width ? "w-$width" : 'w-full';
@endphp

<div class="flex flex-col {{ $widthClass }}">
    {{-- Label --}}
    @if ($label)
        <label for="{{ $name }}" class="mb-1 font-medium text-gray-700 {{ $labelSize }}">
            {{ $label }}
        </label>
    @endif

    {{-- Select --}}
    <select name="{{ $name }}" id="{{ $name }}"
        {{ $attributes->merge([
            'class' =>
                "$selectSize border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-[var(--color-accent)] focus:border-[var(--color-accent)] transition-colors duration-200 " .
                ($readonly ? 'cursor-not-allowed bg-gray-100 text-gray-500' : ''),
        ]) }}
        @if ($readonly) onmousedown="return false;"
        onkeydown="return false;" @endif>
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    {{-- Error --}}
    @if ($error)
        <span class="text-red-600 text-xs mt-1">{{ $error }}</span>
    @endif
</div>
