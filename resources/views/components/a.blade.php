@props([
    'variant' => 'dark', // default variant
])

@php
$colors = [
    'light'   => 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-100',
    'dark'    => 'bg-gray-800 text-white hover:bg-gray-900',
    'success' => 'bg-green-500 text-white hover:bg-green-600',
    'error'   => 'bg-red-500 text-white hover:bg-red-600',
    'info'    => 'bg-blue-500 text-white hover:bg-blue-600',
    'warning' => 'bg-yellow-400 text-white hover:bg-yellow-500',
];

$colorClass = $colors[$variant] ?? $colors['success'];
@endphp

<a {{ $attributes->merge(['class' => "$colorClass cursor-pointer text-white text-xs px-2 py-1 mx-1 rounded"]) }}>
    {{ $slot }}
</a>
