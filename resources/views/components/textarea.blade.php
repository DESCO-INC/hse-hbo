@props([
    'label' => '',
    'name',
    'value' => '',
    'rows' => 4,
])

@php
    $error = $errors->first($name);
@endphp

<div class="flex flex-col">
    {{-- Label --}}
    @if($label)
        <label for="{{ $name }}" class="text-sm font-semibold text-gray-500 mb-1">
            {{ $label }}
        </label>
    @endif

    {{-- Textarea --}}
    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        {{ $attributes->merge([
            'class' => 'px-2 py-1.5 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-[var(--color-accent)] focus:border-[var(--color-accent)] transition-colors duration-200 resize-none'
        ]) }}
    >{{ old($name, $value) }}</textarea>

    {{-- Error --}}
    @if($error)
        <span class="text-red-600 text-xs mt-1">{{ $error }}</span>
    @endif
</div>