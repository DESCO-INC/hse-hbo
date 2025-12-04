<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('images/icon.png') }}" type="image/png">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        /* Background Image with Blur and Gradient Overlay */
        .bg-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ asset('images/background.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: blur(3px);
        }

        .bg-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.75) 0%, rgba(16, 185, 129, 0.65) 50%, rgba(5, 150, 105, 0.75) 100%);
        }
    </style>
</head>

<body class="min-h-screen">
    <!-- Background Image with Effects -->
    <div class="bg-wrapper">
        <div class="bg-image"></div>
        <div class="bg-overlay"></div>
    </div>

    @if (session('success'))
        <div id="toast-success"
            class="fixed top-4 right-4 z-50 flex items-center w-full max-w-xs p-4 text-gray-500 bg-white rounded-lg shadow-lg border border-green-400"
            role="alert">
            <svg class="flex-shrink-0 w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 01.083 1.32l-.083.094L9 14.414 4.707 10.12a1 1 0 011.32-1.497l.094.083L9 11.586l7.293-7.293a1 1 0 011.414 0z"
                    clip-rule="evenodd" />
            </svg>
            <div class="ml-3 text-sm font-medium text-green-700">
                {{ session('success') }}
            </div>
            <button type="button"
                class="ml-auto -mx-1.5 -my-1.5 text-gray-400 hover:text-gray-600 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8"
                onclick="document.getElementById('toast-success').remove()">
                ✖
            </button>
        </div>

        <script>
            setTimeout(() => {
                const toast = document.getElementById('toast-success');
                if (toast) toast.remove();
            }, 4000);
        </script>
    @endif
    @if (session('error'))
        <div id="toast-error"
            class="fixed top-4 right-4 z-50 flex items-center w-full max-w-xs p-4 text-gray-500 bg-white rounded-lg shadow-lg border border-red-400"
            role="alert">
            ❌
            <div class="ml-3 text-sm font-medium text-red-700">
                {{ session('error') }}
            </div>
            <button type="button"
                class="ml-auto -mx-1.5 -my-1.5 text-gray-400 hover:text-gray-600 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8"
                onclick="document.getElementById('toast-error').remove()">
                ✖
            </button>
        </div>

        <script>
            setTimeout(() => {
                const toast = document.getElementById('toast-error');
                if (toast) toast.remove();
            }, 4000);
        </script>
    @endif

    <!-- Clean Green Navbar -->
    <nav class="bg-green-500 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left Section (Logo + Nav Links) -->
                <div class="flex items-center space-x-8">
                    <!-- Logo -->
                    <a href="{{ route('hbo.index') }}" class="text-2xl font-bold text-white">DESCO</a>

                    <!-- Nav Links -->
                    <div class="flex items-center space-x-6 ml-8">
                        @auth
                            <div class="relative" id="hboDropdownContainer">
                                <!-- Main Button -->
                                <x-nav-link type="button" id="hboDropdownButton" :active="request()->routeIs('hbo.index') || request()->routeIs('hbo.list')">
                                    HBO
                                    <svg class="ml-1 w-4 h-4 inline-block" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </x-nav-link>

                                <!-- Dropdown Menu -->
                                <div id="hboDropdownMenu"
                                    class="hidden absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white z-50">
                                    <a href="{{ route('hbo.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        HBO Dashboard
                                    </a>
                                    <a href="{{ route('hbo.list') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        HBO List
                                    </a>
                                </div>
                            </div>

                            <div class="relative" id="pobDropdownContainer">
                                <!-- Main Button -->
                                <x-nav-link type="button" id="pobDropdownButton" :active="request()->routeIs('pob.index') || request()->routeIs('pob.list')">
                                    POB
                                    <svg class="ml-1 w-4 h-4 inline-block" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </x-nav-link>

                                <!-- Dropdown Menu -->
                                <div id="pobDropdownMenu"
                                    class="hidden absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white z-50">
                                    <a href="{{ route('pob.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        POB Dashboard
                                    </a>
                                    <a href="{{ route('pob.list') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        POB List
                                    </a>
                                </div>
                            </div>

                            <div class="relative" id="maintenanceDropdownContainer">
                                <!-- Main Button -->
                                <x-nav-link type="button" id="maintenanceDropdownButton" :active="request()->routeIs('maintenance.index')">
                                    System Maintenance
                                    <svg class="ml-1 w-4 h-4 inline-block" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </x-nav-link>

                                <!-- Dropdown Menu -->
                                <div id="maintenanceDropdownMenu"
                                    class="hidden absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white z-50">
                                    <a href="{{ route('maintenance.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        User Maintenance
                                    </a>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>

                <!-- Right Section -->
                <div class="hidden md:flex items-center ml-auto space-x-4">
                    @guest
                        <a href="{{ url('/') }}"
                            class="text-white hover:text-green-100 transition mx-5 {{ request()->is('/') ? 'font-semibold' : '' }}">
                            Login
                        </a>
                    @endguest

                    @auth
                        <!-- Profile Dropdown -->
                        <div class="relative">
                            <!-- Profile Button -->
                            <button type="button" id="profileDropdownButton"
                                class="flex items-center gap-2 px-4 py-2 bg-white text-green-600 rounded-md hover:bg-green-50 transition font-medium">
                                <!-- Avatar -->
                                <div
                                    class="w-6 h-6 rounded-full bg-green-600 text-white flex items-center justify-center font-semibold text-[10px] uppercase">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>

                                <!-- User Name -->
                                <span class="text-sm">{{ Auth::user()->name }}</span>
                            </button>

                            <!-- Dropdown Menu (same width, right-aligned, below button) -->
                            <div id="profileDropdownMenu"
                                class="hidden absolute top-full right-0 mt-2 w-full bg-white border border-gray-200 rounded-md shadow-lg overflow-hidden z-50">
                                @if (Auth::user()->credentials === 'superadmin')
                                    <a href="{{ route('maintenance.index') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 text-right">
                                        System Maintenance
                                    </a>
                                @endif

                                <button type="button"
                                    class="w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600"
                                    onclick="document.getElementById('logout-modal').classList.remove('hidden')">
                                    Logout
                                </button>
                            </div>
                        </div>

                        <!-- Logout Modal -->
                        <div id="logout-modal"
                            class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/30">
                            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
                                <h2 class="text-xl font-semibold text-gray-800">Confirm Logout</h2>
                                <p class="mt-2 text-sm text-gray-600">Are you sure you want to log out?</p>

                                <div class="mt-6 flex justify-end space-x-3">
                                    <button type="button"
                                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition"
                                        onclick="document.getElementById('logout-modal').classList.add('hidden')">
                                        Cancel
                                    </button>

                                    <form method="POST" action="{{ url('/logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                                            Log out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endauth
                </div>


                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
                        class="text-white hover:text-green-100 focus:outline-none p-2">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-green-600/95">
            <div class="px-2 pt-2 pb-3 space-y-1">
                @auth
                    <x-nav-link href="{{ route('hbo.index') }}" :active="request()->routeIs('hbo.index')"
                        class="block text-white hover:bg-green-700 rounded-md">
                        HBO Dashboard
                    </x-nav-link>
                    <x-nav-link href="{{ route('hbo.list') }}" :active="request()->routeIs('hbo.list')"
                        class="block text-white hover:bg-green-700 rounded-md">
                        HBO List
                    </x-nav-link>
                @endauth

                @guest
                    <a href="{{ url('/') }}" class="block px-3 py-2 text-white hover:bg-green-700 rounded-md">
                        Login
                    </a>
                    <a href="{{ url('/register') }}" class="block px-3 py-2 text-white hover:bg-green-700 rounded-md">
                        Register
                    </a>
                @endguest
            </div>
        </div>
    </nav>


    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>
    @stack('scripts')
    <!-- Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Collect all dropdowns including the new Maintenance dropdown
            const dropdowns = [{
                    button: document.getElementById('profileDropdownButton'),
                    menu: document.getElementById('profileDropdownMenu')
                },
                {
                    button: document.getElementById('hboDropdownButton'),
                    menu: document.getElementById('hboDropdownMenu')
                },
                {
                    button: document.getElementById('pobDropdownButton'),
                    menu: document.getElementById('pobDropdownMenu')
                },
                {
                    button: document.getElementById('maintenanceDropdownButton'),
                    menu: document.getElementById('maintenanceDropdownMenu') // ← note your typo kept
                }
            ];

            // Filter out null dropdowns (prevents errors if some buttons are not on the page)
            const validDropdowns = dropdowns.filter(d => d.button && d.menu);

            // Toggle logic
            validDropdowns.forEach(({
                button,
                menu
            }) => {
                button.addEventListener('click', (e) => {
                    e.stopPropagation();

                    // Close all other dropdowns
                    validDropdowns.forEach(({
                        menu: otherMenu
                    }) => {
                        if (otherMenu !== menu) {
                            otherMenu.classList.add('hidden');
                        }
                    });

                    // Toggle current
                    menu.classList.toggle('hidden');
                });

                // Prevent closing when clicking inside
                menu.addEventListener('click', (e) => e.stopPropagation());
            });

            // Close all on outside click
            document.addEventListener('click', () => {
                validDropdowns.forEach(({
                    menu
                }) => menu.classList.add('hidden'));
            });
        });
    </script>


</body>

</html>
