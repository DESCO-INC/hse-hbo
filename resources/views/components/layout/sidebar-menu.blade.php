@props([
    'label',
    'icon' => null,
    'id' => null,
    'links' => [],
    'open' => false, // submenu open by default
])

@php
    $menuId = $id ?? 'sidebarDropdown_' . md5($label);
    $currentUrl = trim(request()->path(), '/');

    // Highlight dropdown button if any child route matches
    $isDropdownActive = false;
    foreach ($links as $link) {
        if (!empty($link['route'])) {
            $routeUrl = trim(parse_url(route($link['route']), PHP_URL_PATH), '/');
            if ($currentUrl === $routeUrl) {
                $isDropdownActive = true;
                break;
            }
        }
    }

    // Determine if submenu should be open
    $isDropdownOpen = $open || $isDropdownActive;
@endphp

<div class="flex flex-col rounded-md {{ $isDropdownOpen
                ? 'bg-[var(--color-secondary)] text-[var(--color-primary)] border-l-4 border-[var(--color-primary)]'
                : 'text-[var(--color-secondary)] hover:bg-[var(--color-secondary)] hover:text-[var(--color-primary)]' }} ">
    {{-- Dropdown Button --}}
    <button type="button" id="{{ $menuId }}_btn"
        class="flex items-center px-2.5 py-2 text-sm font-semibold rounded-md w-full text-left transition-colors duration-200">

        @if ($icon)
            <x-dynamic-component :component="$icon" class="w-5 h-5 mr-2" />
        @endif

        <span class="sidebar-link-label flex-1">{{ $label }}</span>

        <svg class="w-4 h-4 ml-2 transform transition-transform duration-200 {{ $isDropdownOpen ? 'rotate-180' : '' }}" 
             id="{{ $menuId }}_icon" fill="none"
             stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Dropdown Menu --}}
    <div id="{{ $menuId }}_submenu"
        class="p-2 mt-1 flex flex-col space-y-1 transition-all duration-200">
        @foreach ($links as $link)
            @php
                $href = $link['route'] ? ($link['route'] === '#' ? '#' : route($link['route'])) : '#';
                $routeUrl = $link['route'] ? trim(parse_url(route($link['route']), PHP_URL_PATH), '/') : '';
                $isLinkActive = $currentUrl === $routeUrl;
            @endphp

            <a href="{{ $href }}"
                class="flex items-center px-2 py-1 text-sm rounded-md transition-colors duration-150
                    {{ $isDropdownOpen
                        ? 'bg-[var(--color-primary)] text-[var(--color-secondary)] border-l-2 border-[var(--color-primary)]'
                        : 'text-[var(--color-secondary)] hover:bg-gray-100 hover:text-[var(--color-primary)]' }}">
                @if (!empty($link['icon']))
                    <x-dynamic-component :component="$link['icon']" class="w-4 h-4 mr-2" />
                @endif
                <span class="sidebar-link-label">{{ $link['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('{{ $menuId }}_btn');
    const submenu = document.getElementById('{{ $menuId }}_submenu');
    const icon = document.getElementById('{{ $menuId }}_icon');

    btn.addEventListener('click', function(e) {
        e.preventDefault();
        submenu.classList.toggle('hidden');
        submenu.classList.toggle('bg-white');
        submenu.classList.toggle('shadow');
        submenu.classList.toggle('rounded-md');
        submenu.classList.toggle('px-2');
        submenu.classList.toggle('py-1');
        icon.classList.toggle('rotate-180');
    });
});
</script>