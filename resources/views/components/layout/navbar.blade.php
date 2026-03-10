{{-- Top Bar --}}
<header
    class="h-16 bg-[var(--color-secondary)]/70 backdrop-blur border-b border-gray-200 flex items-center justify-between px-6">

    <h1 class="text-lg font-semibold text-[var(--color-text-primary)]">
        {{ config('app.name') }}
    </h1>

    @auth
        <div class="relative">
            <button type="button" id="profileDropdownButton"
                class="flex items-center gap-2 px-4 py-2 bg-[var(--color-primary)] text-white rounded-md hover:bg-[var(--color-primary-hover)] transition">

                <div
                    class="w-7 h-7 rounded-full bg-[var(--color-secondary)]  text-[var(--color-primary)] flex items-center justify-center font-bold text-xs">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>

                <span class="text-sm font-semibold text-[var(--color-secondary)] ">{{ Auth::user()->name }}</span>
            </button>

            <div id="profileDropdownMenu"
                class="hidden absolute right-0 mt-2 w-40 bg-white rounded-md shadow-lg border border-gray-200 overflow-hidden z-50">

                <a href="{{ route('maintenance.profile') }}"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-right">
                    Profile
                </a>

                <button type="button" class="w-full text-right px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                    onclick="document.getElementById('logout-modal').classList.remove('hidden')">
                    Logout
                </button>
            </div>
        </div>
    @endauth
</header>


{{-- Logout Modal --}}
<div id="logout-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">

    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <h2 class="text-lg font-semibold text-gray-800">Confirm Logout</h2>
        <p class="mt-2 text-sm text-gray-600">Are you sure you want to log out?</p>

        <div class="mt-6 flex justify-end gap-3">
            <button type="button" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300"
                onclick="document.getElementById('logout-modal').classList.add('hidden')">
                Cancel
            </button>

            <form id="logout-form" method="POST" action="{{ url('/logout') }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Log out
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const profileBtn = document.getElementById('profileDropdownButton');
        const profileMenu = document.getElementById('profileDropdownMenu');

        if (profileBtn && profileMenu) {
            profileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                profileMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', function() {
                profileMenu.classList.add('hidden');
            });
        }

        setTimeout(() => {
            document.getElementById('toast-success')?.remove();
            document.getElementById('toast-error')?.remove();
        }, 4000);
    });


    $('#logout-form').on('submit', function() {
        localStorage.clear();
    });
</script>
