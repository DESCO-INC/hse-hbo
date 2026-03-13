<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - 404 Not Found</title>
    <link rel="icon" href="{{ asset('images/icon.png') }}" type="image/png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
</head>

<body class="relative min-h-screen overflow-hidden flex flex-col">

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

    <div class="flex-1 flex flex-col">
        {{-- Top Bar --}}
        @auth
            <x-layout.navbar />
        @endauth

        {{-- Main Content --}}
        <main class="flex-1 flex flex-col justify-center items-center text-center p-6">
            <h1 class="text-9xl font-bold text-white drop-shadow-lg">404</h1>
            <p class="mt-4 text-xl text-white/90">Oops! The page you are looking for was not found.</p>
            <a href="{{ route('login') }}"
                class="mt-6 inline-block px-6 py-3 bg-white text-green-700 font-semibold rounded-lg shadow-lg hover:bg-green-100 transition">
                Go back to Home
            </a>
        </main>
    </div>

</body>

</html>