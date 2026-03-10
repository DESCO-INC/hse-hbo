@php
    // Determine which type exists in the session
    $sessionTypes = ['success', 'error', 'info', 'warning', 'default'];
    $type = 'default';
    $message = null;

    foreach ($sessionTypes as $t) {
        if (session()->has($t)) {
            $type = $t;
            $message = session($t);
            break;
        }
    }

    // Tailwind colors for each type
    $colors = [
        'default' => 'bg-gray-800/90 text-white',
        'success' => 'bg-green-500/90 text-white',
        'info'    => 'bg-blue-500/90 text-white',
        'warning' => 'bg-yellow-400/90 text-black',
        'error'   => 'bg-red-500/90 text-white',
    ];

    $toastClass = $colors[$type] ?? $colors['default'];
@endphp

@if($message)
<div
    class="toast fixed top-20 right-5 z-50 p-4 rounded-lg shadow-lg flex items-center space-x-3 {{ $toastClass }}"
>
    {{-- Optional Icon for each type --}}
    @if($type === 'success') <span>✅</span> @endif
    @if($type === 'info')    <span>ℹ️</span> @endif
    @if($type === 'warning') <span>⚠️</span> @endif
    @if($type === 'error')   <span>❌</span> @endif

    <div class="flex-1">{{ $message }}</div>
    <button class="font-bold toast-close">&times;</button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toast = document.querySelector('.toast');
        const closeBtn = document.querySelector('.toast-close');

        // Auto-hide after 3 seconds
        setTimeout(() => {
            toast?.remove();
        }, 3000);

        // Manual close
        closeBtn?.addEventListener('click', () => {
            toast?.remove();
        });
    });
</script>
@endif