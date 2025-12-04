<!-- resources/views/components/dashboard-card.blade.php -->
@props([
    'title',
])

<div {{ $attributes->merge([
    'class' => 'h-full col-span-1 bg-white rounded-lg shadow-md border p-6 hover:shadow-lg transition-shadow duration-200'
]) }}>
    <div class="flex h-full items-center justify-between">
        <div class="flex-1 h-full">
            <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">{{ $title }}</p>
            {{ $slot }}
        </div>
    </div>
</div>