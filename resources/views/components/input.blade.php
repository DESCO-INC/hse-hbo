@props([
    'name',
    'id' => null,
    'type' => 'text',
    'value' => null,
])

<label 
    for="{{ $id ?? $name }}"
    {{ $attributes->merge(['class' => 'block text-sm font-medium text-gray-700 mb-2']) }}>
    {{ $slot }}
</label>

<input 
    type="{{ $type }}"
    name="{{ $name }}"
    id="{{ $id ?? $name }}"
    value="{{ old($name, $value) }}"
    {{ $attributes->merge([
        'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none'
    ]) }}
>

@error($name)
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
@enderror