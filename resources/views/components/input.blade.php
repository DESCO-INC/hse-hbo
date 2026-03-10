@props([
    'label' => '',
    'name',
    'type' => 'text',
    'value' => '',
    'hidden' => false,
    'size' => 'md',   
    'width' => 'full', 
    'readonly' => false,
])

@php
    $error = $errors->first($name);

    // Size variants
    $sizes = [
        'sm' => ['input' => 'px-2 py-1 text-sm', 'label' => 'text-xs'],
        'md' => ['input' => 'px-2 py-1.5 text-sm', 'label' => 'text-sm'],
        'lg' => ['input' => 'px-3 py-2 text-base', 'label' => 'text-base'],
    ];

    $inputSize = $sizes[$size]['input'] ?? $sizes['md']['input'];
    $labelSize = $sizes[$size]['label'] ?? $sizes['md']['label'];

    // Width class
    $widthClass = $width ? "w-$width" : "w-full";
@endphp

@if(!$hidden)
<div class="flex flex-col {{ $widthClass }}">
    {{-- Label --}}
    @if($label)
        <label for="{{ $name }}" class="mb-1 font-semibold text-gray-500 {{ $labelSize }}">
            {{ $label }}
        </label>
    @endif

    {{-- Input --}}
    <input 
        type="{{ $type }}" 
        name="{{ $name }}" 
        id="{{ $name }}" 
        value="{{ old($name, $value) }}"
        {{ $attributes->merge([
            'class' => "$inputSize border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-[var(--color-accent)] focus:border-[var(--color-accent)] transition-colors duration-200"
        ]) }}
        @if ($readonly) readonly @endif
    />

    {{-- Error --}}
    @if($error)
        <span class="text-red-600 text-xs mt-1">{{ $error }}</span>
    @endif
</div>
@endif