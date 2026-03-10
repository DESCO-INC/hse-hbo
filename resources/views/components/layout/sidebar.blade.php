<aside id="sidebar" class="w-60 bg-[var(--color-primary)] flex flex-col transition-all duration-300">

    <!-- Logo + Collapse Button -->
    <div id="sidebarLogo" class="h-16 flex items-center px-4 border-b border-gray-200 transition-all duration-300">
        <span class="text-xl font-bold text-[var(--color-secondary)] flex-1" style="font-family: Verdana, sans-serif;">
            DESCO
        </span>

        {{-- Collapse button when expanded --}}
        <button id="sidebarToggleBtn" class="p-1 rounded hover:bg-gray-200">
            <x-heroicon-o-chevron-double-left class="w-5 h-5 toggle-icon-left text-[var(--color-secondary)]" />
            <x-heroicon-o-chevron-double-right class="w-5 h-5 toggle-icon-right text-[var(--color-secondary)] hidden" />
        </button>
    </div>

    <!-- Menu -->
    <nav class="flex-1 p-4 space-y-2" id="sidebarMenu">
        <!-- Separator Header -->
        <div class="sidebar-header mb-1 px-2 text-xs font-semibold text-white/70 uppercase">
            HBO Section
        </div>
        <x-layout.sidebar-link route="hbo.index" label="HBO Dashboard" icon="heroicon-s-presentation-chart-bar" />
        <x-layout.sidebar-link route="hbo.list" label="HBO List" icon="heroicon-s-list-bullet" />

        <!-- Separator Header -->
        <div class="sidebar-header mt-3 mb-1 px-2 text-xs font-semibold text-white/70 uppercase">
            POB Section
        </div>
        <x-layout.sidebar-link route="pob.index" label="POB Dashboard" icon="heroicon-s-presentation-chart-line" />
        <x-layout.sidebar-link route="pob.list" label="POB List" icon="heroicon-s-numbered-list" />

        @if (Auth::user()->credentials == 'SUPER_ADMIN')
            <!-- Separator Header -->
            <div class="sidebar-header mt-3 mb-1 px-2 text-xs font-semibold text-white/70 uppercase">
                Maintenance
            </div>
            <x-layout.sidebar-link route="maintenance.user" label="User Settings" icon="heroicon-s-user-group" />
            <x-layout.sidebar-link route="maintenance.organization" label="B.U. Settings" icon="heroicon-s-home" />
            <x-layout.sidebar-link route="maintenance.audit_trail" label="Audit Trail" icon="heroicon-s-clock" />
        @endif
    </nav>

    <!-- Footer -->
    <div id="footer"
        class="px-4 py-2 border-t border-gray-200 text-sm text-[var(--color-secondary)] flex justify-center">
        © 2026 DESCO
    </div>
</aside>

<script>
    $(document).ready(function() {
        const $sidebar = $('#sidebar');
        const $toggleBtn = $('#sidebarToggleBtn'); // button next to logo
        const $menu = $('#sidebarMenu');
        const $logo = $('#sidebarLogo');
        const $footer = $('#footer');
        const $logoSpan = $logo.find('span');
        const $headers = $('.sidebar-header');

        // Store the full text in a data attribute once
        if (!$logoSpan.data('full')) {
            $logoSpan.attr('data-full', $logoSpan.text().trim());
        }

        // Function to get initials
        function getInitials(text) {
            return text
                .split(' ')
                .map(word => word.charAt(0).toUpperCase())
                .join('');
        }

        function toggleSidebar() {
            // Toggle sidebar width
            $sidebar.toggleClass('w-60 w-25'); // adjust w-25 to your collapsed width

            // Hide/show labels
            $menu.find('.sidebar-link-label').toggleClass('hidden');
            $headers.toggleClass('hidden');

            // Shrink logo text
            $logoSpan.addClass('text-center');
            $footer.toggleClass('text-sm text-[8px]');

            // Swap logo text with initials when minimized
            if ($sidebar.hasClass('w-25')) { // collapsed
                $logoSpan.text(getInitials($logoSpan.data('full')));
            } else { // expanded
                $logoSpan.text($logoSpan.data('full'));
            }

            // Center logo when minimized
            $logo.toggleClass('justify-center');

            // Swap toggle icons
            $toggleBtn.find('.toggle-icon-left, .toggle-icon-right').toggleClass('hidden');
        }

        // Collapse button click
        $toggleBtn.on('click', function(e) {
            e.stopPropagation();
            toggleSidebar();
        });

        // Make the logo itself clickable when minimized
        $logo.on('click', function() {
            if ($sidebar.hasClass('w-25')) { // if minimized
                toggleSidebar();
            }
        });
    });
</script>
