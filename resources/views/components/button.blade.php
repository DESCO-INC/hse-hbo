@props([
    'type' => 'button',
    'variant' => 'accent', // default
    'size' => 'md',        // new: xs, sm, md, lg
    'href' => null,        // optional
])


@php
    // Base styles
    $baseClasses = 'rounded-md transition-colors duration-200 inline-flex items-center justify-center';

    // Variant styles
    $variantClasses = match($variant) {
        'warning' => 'bg-yellow-500 hover:bg-yellow-600 text-white',
        'info'    => 'bg-blue-500 hover:bg-blue-600 text-white',
        'purple'    => 'bg-purple-500 hover:bg-purple-600 text-white',
        'error'   => 'bg-red-500 hover:bg-red-600 text-white',
        'gray'   => 'bg-gray-500 hover:bg-gray-600 text-white',
        default   => 'bg-[var(--color-primary)] hover:bg-[var(--color-primary-hover)] text-white',
    };

    // Size styles
    $sizeClasses = match($size) {
        'xs' => 'px-2 py-0.5 text-xs',
        'sm' => 'px-2.5 py-1 text-xs',
        'md' => 'px-3 py-1.5 text-sm', // default
        'lg' => 'px-4 py-2 text-base',
        default => 'px-3 py-1.5 text-sm',
    };

    $classes = "$baseClasses $variantClasses $sizeClasses";
@endphp

@if($href)
    <a href="{{ $href }}"
       {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}"
            {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif