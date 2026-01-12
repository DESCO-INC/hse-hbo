@props(['active' => false, 'type' => 'a'])

@if ($type === 'button')
    <button 
        {{ $attributes }}
        @class([
            'block px-3 py-2 rounded-md font-semibold transition',
            'text-white bg-green-700' => $active,
            'text-white hover:bg-green-700' => !$active,
        ])
        aria-current="{{ $active ? 'page' : 'false' }}"
    >
        {{ $slot }}
    </button>
@else
    <a 
        {{ $attributes }}
        @class([
            'block px-3 py-2 rounded-md font-semibold transition',
            'text-white bg-green-700' => $active,
            'text-white hover:bg-green-700' => !$active,
        ])
        aria-current="{{ $active ? 'page' : 'false' }}"
    >
        {{ $slot }}
    </a>
@endif
