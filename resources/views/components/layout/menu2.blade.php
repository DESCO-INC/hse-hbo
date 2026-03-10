@props(['label', 'icon' => null, 'id' => null, 'links' => []])

@php
    $currentRoute = request()->url();
    $isActive = collect($links)->contains(fn($link) => isset($link['url']) && url($link['url']) === $currentRoute);
@endphp

<div id="sidebar-dropdown" class="rounded-md">
    <button id="dropdown-toggle"
        class="w-full flex justify-between items-center p-2 text-sm
               text-[var(--color-secondary)] hover:text-[var(--color-primary)]
               bg-[var(--color-primary)] hover:bg-[var(--color-secondary)]
               rounded-md transition-colors duration-200 cursor-pointer">

        <span class="flex items-center">
            @if ($icon)
                <x-dynamic-component :component="$icon" class="w-4 h-4 mr-2" />
            @endif
            {{ $label }}
        </span>

        <span class="arrow">
            <x-heroicon-s-chevron-down class="w-4 h-4 icon-down {{ $isActive ? 'hidden' : '' }}" />
            <x-heroicon-s-chevron-up class="w-4 h-4 icon-up {{ $isActive ? '' : 'hidden' }}" />
        </span>
    </button>

    <div id="dropdown-menu" class="{{ $isActive ? '' : 'hidden' }} mt-1 space-y-1">
        @foreach ($links as $link)
            <a href="{{ $link['url'] ?? '#' }}" class="block px-5 py-2 text-sm text-green-500 hover:text-green-700">
                <span class="flex items-center">
                    <span class="font-bold mr-2">•</span>
                    {{ $link['label'] }}
                </span>
            </a>
        @endforeach
    </div>
</div>

@once
<script>
    $(document).ready(function() {
        $('#dropdown-toggle').click(function() {
            let btn = $(this);

            // Toggle text & background colors
            if (btn.hasClass('text-[var(--color-secondary)]')) {
                btn.removeClass('text-[var(--color-secondary)] bg-[var(--color-primary)]')
                    .addClass('text-[var(--color-primary)] bg-[var(--color-secondary)]');
            } else {
                btn.removeClass('text-[var(--color-primary)] bg-[var(--color-secondary)]')
                    .addClass('text-[var(--color-secondary)] bg-[var(--color-primary)]');
            }

            // Toggle chevron icons
            let arrow = btn.find('.arrow');
            arrow.find('.icon-down').toggleClass('hidden');
            arrow.find('.icon-up').toggleClass('hidden');

            $('#dropdown-menu').toggleClass('hidden');
            $('#sidebar-dropdown').toggleClass('bg-[var(--color-secondary)]');
        });

        // On page load, no animation: just set colors if active
        @if($isActive)
            let btn = $('#dropdown-toggle');
            btn.removeClass('text-[var(--color-secondary)] bg-[var(--color-primary)]')
               .addClass('text-[var(--color-primary)] bg-[var(--color-secondary)]');
            $('#sidebar-dropdown').addClass('bg-[var(--color-secondary)]');
        @endif
    });
</script>
@endonce