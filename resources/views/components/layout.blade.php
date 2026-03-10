<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('images/icon.png') }}" type="image/png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
</head>

<body class="relative min-h-screen overflow-hidden">

    {{-- Background Image --}}
    <div class="fixed inset-0 -z-20">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat blur-sm scale-105"
            style="background-image: url('{{ asset('images/background.jpg') }}');">
        </div>
    </div>

    {{-- Gradient Overlay --}}
    <div
        class="fixed inset-0 -z-10 bg-gradient-to-br 
        from-[rgba(34,197,94,0.75)] 
        via-[rgba(16,185,129,0.65)] 
        to-[rgba(5,150,105,0.75)]">
    </div>

    <div class="flex h-screen overflow-hidden relative z-10">
        {{-- Sidebar --}}
        @auth
            <x-layout.sidebar />
        @endauth

        <!-- ================= Main Area ================= -->
        <div class="flex-1 flex flex-col">
            {{-- Top Bar --}}
            @auth
                <x-layout.navbar />
            @endauth

            <!-- Scrollable Content -->
            <main class="flex-1 overflow-y-auto p-6">
                {{ $slot }}
            </main>
        </div>

        {{-- Toast Notification --}}
        <x-layout.toast />
    </div>

    @stack('scripts')
</body>

</html>
