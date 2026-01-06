<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Registration' }} - MIRAI Hub</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 130 85'%3E%3Cpath d='M0,80 L20,0 L45,0 L25,80 Z' fill='%2306b6d4'/%3E%3Cpath d='M85,80 L105,0 L130,0 L110,80 Z' fill='%233b82f6'/%3E%3Cpath d='M54,85 L34,25 L54,25 L64,50 L84,25 L104,25 Z' fill='white'/%3E%3C/svg%3E">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        input[type="date"]::-webkit-calendar-picker-indicator,
        input[type="datetime-local"]::-webkit-calendar-picker-indicator,
        input[type="time"]::-webkit-calendar-picker-indicator {
            filter: invert(0.5) sepia(1) saturate(5) hue-rotate(150deg);
            cursor: pointer;
            opacity: 0.6;
            transition: 0.2s;
        }

        input[type="date"]::-webkit-calendar-picker-indicator:hover,
        input[type="datetime-local"]::-webkit-calendar-picker-indicator:hover,
        input[type="time"]::-webkit-calendar-picker-indicator:hover {
            opacity: 1;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: slideIn 0.3s ease-out;
        }
    </style>
</head>

<body class="bg-[#050505] text-zinc-100 min-h-screen grid-bg">
    <header class="border-b border-zinc-800 bg-[#050505]/90 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="/">
                <x-mirai-logo size="md" />
            </a>

            @auth
                <div class="flex items-center gap-4" x-data="{ open: false }">
                    <div class="relative">
                        <button @click="open = !open"
                            class="flex items-center gap-2 hover:opacity-80 transition cursor-pointer">
                            <div class="shrink-0 grow-0 rounded-full bg-[#222] border border-gray-700 overflow-hidden"
                                style="width: 36px; height: 36px;">
                                @if (auth()->user()->avatar)
                                    @if (str_starts_with(auth()->user()->avatar, 'http'))
                                        <img src="{{ auth()->user()->avatar }}" class="object-cover"
                                            style="width: 36px; height: 36px;">
                                    @else
                                        <img src="{{ Storage::url(auth()->user()->avatar) }}" class="object-cover"
                                            style="width: 36px; height: 36px;">
                                    @endif
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <span
                                class="text-sm text-gray-300">{{ Str::before(auth()->user()->name, ' ') ?: auth()->user()->name }}</span>
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown -->
                        <div x-show="open" @click.outside="open = false" x-transition x-cloak
                            class="absolute right-0 mt-3 w-52 bg-[#1a1a1a] border border-gray-800 rounded-xl shadow-2xl py-3 z-50">
                            <a href="{{ route('profile') }}"
                                class="flex items-center gap-4 px-6 py-3 text-sm text-gray-300 hover:bg-[#222] hover:text-cyan-400 transition cursor-pointer whitespace-nowrap">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Profil Saya
                            </a>
                            @if (auth()->user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}"
                                    class="flex items-center gap-4 px-6 py-3 text-sm text-gray-300 hover:bg-[#222] hover:text-cyan-400 transition cursor-pointer whitespace-nowrap">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Admin Panel
                                </a>
                            @endif
                            <div class="border-t border-gray-700/50 my-2 mx-4"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex items-center gap-4 w-full px-6 py-3 text-sm text-red-400 hover:bg-[#222] transition cursor-pointer whitespace-nowrap">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}"
                    class="text-sm text-gray-400 hover:text-cyan-400 transition cursor-pointer">Login</a>
            @endauth
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8">
        {{ $slot }}
    </main>

    <footer class="border-t border-white/5 mt-16 bg-black/50">
        <div class="max-w-4xl mx-auto px-4 py-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <x-mirai-logo size="sm" />
            <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} MIRAI Hub. Powered by MIRAI Indonesia.</p>
        </div>
    </footer>

    @livewireScripts
</body>

</html>
