<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Izinkan evaluasi skrip untuk Alpine/Livewire pada dev (CSP loosen) --}}
    <meta http-equiv="Content-Security-Policy"
        content="default-src 'self'; script-src 'self' 'unsafe-eval' 'unsafe-inline' data:; style-src 'self' 'unsafe-inline'; img-src 'self' data: blob:; connect-src 'self' ws: wss:;">
    <title>{{ $title ?? 'Admin' }} - MIRAI Hub</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 130 85'%3E%3Cpath d='M0,80 L20,0 L45,0 L25,80 Z' fill='%2306b6d4'/%3E%3Cpath d='M85,80 L105,0 L130,0 L110,80 Z' fill='%233b82f6'/%3E%3Cpath d='M54,85 L34,25 L54,25 L64,50 L84,25 L104,25 Z' fill='white'/%3E%3C/svg%3E">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        /* Sidebar toggle using CSS */
        #sidebar-toggle:checked~.sidebar-wrapper .sidebar {
            transform: translateX(0);
        }

        #sidebar-toggle:checked~.sidebar-wrapper .sidebar-overlay {
            opacity: 1;
            pointer-events: auto;
        }

        #sidebar-toggle:not(:checked)~.sidebar-wrapper .sidebar {
            transform: translateX(-100%);
        }

        #sidebar-toggle:not(:checked)~.sidebar-wrapper .sidebar-overlay {
            opacity: 0;
            pointer-events: none;
        }
    </style>
</head>

<body class="bg-[#050505] text-gray-100 min-h-screen grid-bg">
    <!-- Hidden checkbox for sidebar toggle -->
    <input type="checkbox" id="sidebar-toggle" class="hidden">

    <div class="sidebar-wrapper">
        <!-- Sidebar Overlay -->
        <label for="sidebar-toggle"
            class="sidebar-overlay fixed inset-0 bg-black/60 backdrop-blur-sm z-40 transition-opacity duration-300 cursor-pointer">
        </label>

        <!-- Sidebar -->
        <aside
            class="sidebar w-64 bg-[#0a0a0a] border-r border-gray-800 fixed h-full z-50 transition-transform duration-300 ease-in-out">
            <!-- Close button -->
            <label for="sidebar-toggle"
                class="absolute top-4 right-4 p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </label>

            <div class="p-6">
                <a href="{{ route('admin.dashboard') }}">
                    <x-mirai-logo size="md" />
                </a>
            </div>

            <nav class="px-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition cursor-pointer {{ request()->routeIs('admin.dashboard') ? 'bg-cyan-500/10 text-cyan-400 border-l-2 border-cyan-500' : 'text-gray-400 hover:bg-[#1a1a1a] hover:text-gray-100' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.tournaments.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition cursor-pointer {{ request()->routeIs('admin.tournaments.*') ? 'bg-cyan-500/10 text-cyan-400 border-l-2 border-cyan-500' : 'text-gray-400 hover:bg-[#1a1a1a] hover:text-gray-100' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Tournaments
                </a>
            </nav>

            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-800">
                <div class="flex items-center gap-3 px-4 py-2">
                    <div class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center">
                        <span class="text-sm font-medium">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ auth()->user()->name ?? 'User' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-red-400 transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>
    </div>

    <!-- Main Content -->
    <main class="min-h-screen">
        <!-- Top Bar with Menu Button -->
        <div class="sticky top-0 z-40 bg-[#050505]/90 backdrop-blur-md border-b border-gray-800/50">
            <div class="flex items-center gap-4 px-4 py-3">
                <label for="sidebar-toggle"
                    class="p-2 text-gray-400 hover:text-cyan-400 hover:bg-gray-800/50 rounded-lg transition cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </label>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                    <x-mirai-logo size="sm" />
                </a>
            </div>
        </div>

        <div class="p-4 md:p-6">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-500/10 border border-green-500/30 rounded-lg text-green-400 flex items-center gap-3"
                    x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-lg text-red-400 flex items-center gap-3"
                    x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>

    @livewireScripts
</body>

</html>
