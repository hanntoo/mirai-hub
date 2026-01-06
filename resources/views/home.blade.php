<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIRAI Hub - Portal Turnamen MIRAI Indonesia</title>
    <meta name="description" content="Portal pendaftaran turnamen esports MIRAI Indonesia. Daftar dan ikuti turnamen Mobile Legends, Valorant, PUBG, dan game lainnya.">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 130 85'%3E%3Cpath d='M0,80 L20,0 L45,0 L25,80 Z' fill='%2306b6d4'/%3E%3Cpath d='M85,80 L105,0 L130,0 L110,80 Z' fill='%233b82f6'/%3E%3Cpath d='M54,85 L34,25 L54,25 L64,50 L84,25 L104,25 Z' fill='white'/%3E%3C/svg%3E">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#050505] text-zinc-100 overflow-x-hidden" x-data>
    <!-- Header -->
    <header class="fixed w-full z-50 bg-[#050505]/90 backdrop-blur-md border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="/">
                    <x-mirai-logo size="md" />
                </a>
                
                <div class="hidden md:flex items-center gap-8">
                    <a href="#home" class="text-sm text-gray-400 hover:text-cyan-400 transition">Home</a>
                    <a href="#tournaments" class="text-sm text-gray-400 hover:text-cyan-400 transition">Tournament</a>
                    <a href="#about" class="text-sm text-gray-400 hover:text-cyan-400 transition">About</a>
                    <a href="https://mirai-id.netlify.app/" target="_blank" class="text-sm text-gray-400 hover:text-cyan-400 transition">Company Profile</a>
                </div>
                
                <div class="flex items-center gap-4">
                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 hover:opacity-80 transition cursor-pointer">
                                <div class="w-9 h-9 rounded-full bg-[#222] border border-gray-700 overflow-hidden flex items-center justify-center">
                                    @if(auth()->user()->avatar)
                                        @if(str_starts_with(auth()->user()->avatar, 'http'))
                                            <img src="{{ auth()->user()->avatar }}" class="w-full h-full object-cover">
                                        @else
                                            <img src="{{ Storage::url(auth()->user()->avatar) }}" class="w-full h-full object-cover">
                                        @endif
                                    @else
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    @endif
                                </div>
                                <span class="text-sm hidden sm:block text-gray-300">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            
                            <!-- Dropdown -->
                            <div x-show="open" @click.away="open = false" x-transition
                                 class="absolute right-0 mt-2 w-48 bg-[#1a1a1a] border border-gray-800 rounded-lg shadow-xl py-1 z-50">
                                <a href="{{ route('profile') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:bg-[#222] hover:text-cyan-400 transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Profil Saya
                                </a>
                                @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:bg-[#222] hover:text-cyan-400 transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Admin Panel
                                </a>
                                @endif
                                <div class="border-t border-gray-800 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-400 hover:bg-[#222] transition cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-cyan-400 transition cursor-pointer">Login</a>
                        <a href="{{ route('auth.register') }}" class="bg-gradient-to-r from-cyan-500 to-blue-600 text-white px-4 py-2 rounded text-sm font-bold hover:opacity-90 transition shadow-[0_0_15px_rgba(6,182,212,0.3)] cursor-pointer">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>
    
    <!-- Hero - Full Screen -->
    <section id="home" class="min-h-screen flex items-center justify-center pt-20 relative overflow-hidden grid-bg">
        <!-- Glow Effect -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-cyan-500/20 rounded-full blur-[120px] pointer-events-none"></div>
        
        <div class="text-center px-4 max-w-5xl mx-auto z-10 relative">
            <div class="mb-4 inline-block">
                <span class="bg-[#111] border border-cyan-500/30 text-cyan-400 text-[10px] md:text-xs font-bold px-4 py-1 rounded-full uppercase tracking-[0.3em]">Portal Turnamen Esports</span>
            </div>
            
            <!-- Master Logo -->
            <div class="w-full max-w-xl mx-auto mb-6 transform hover:scale-105 transition duration-500">
                <svg viewBox="0 0 500 150" xmlns="http://www.w3.org/2000/svg" class="w-full drop-shadow-[0_0_30px_rgba(6,182,212,0.3)]">
                    <defs>
                        <linearGradient id="heroGrad" x1="0" y1="0" x2="130" y2="0" gradientUnits="userSpaceOnUse">
                            <stop offset="0%" style="stop-color:#06b6d4;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:1" />
                        </linearGradient>
                    </defs>
                    <g transform="translate(40, 35)">
                        <path d="M0,80 L20,0 L45,0 L25,80 Z" fill="url(#heroGrad)" />
                        <path d="M85,80 L105,0 L130,0 L110,80 Z" fill="url(#heroGrad)" />
                        <path d="M54,85 L34,25 L54,25 L64,50 L84,25 L104,25 Z" fill="white" />
                    </g>
                    <text x="180" y="105" font-family="'Orbitron', sans-serif" font-weight="900" font-size="80" fill="white" letter-spacing="-2">IRAI</text>
                    <rect x="185" y="120" width="280" height="4" fill="#333" />
                    <rect x="185" y="120" width="80" height="4" fill="url(#heroGrad)" />
                    <circle cx="480" cy="122" r="4" fill="#3b82f6" />
                </svg>
                <p class="text-cyan-400 font-display text-lg md:text-xl tracking-widest mt-2">HUB</p>
            </div>
            
            <p class="text-gray-400 text-sm md:text-lg max-w-2xl mx-auto mb-10 leading-relaxed">
                Portal pendaftaran turnamen esports dari MIRAI Indonesia.<br>
                <span class="text-white font-semibold">Temukan. Daftar. Bertanding.</span>
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#tournaments" class="bg-gradient-to-r from-cyan-500 to-blue-600 text-white px-8 py-3 rounded font-display font-bold hover:opacity-90 transition shadow-[0_0_20px_rgba(6,182,212,0.4)] cursor-pointer">Lihat Tournament</a>
                <a href="https://mirai-id.netlify.app/" target="_blank" class="border border-gray-600 text-white px-8 py-3 rounded-lg hover:border-cyan-500 hover:bg-cyan-500/10 transition cursor-pointer">Tentang MIRAI</a>
            </div>
        </div>
    </section>
    
    <!-- Tournaments -->
    <section id="tournaments" class="py-24 px-6 bg-black border-y border-white/5">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-display font-bold text-white mb-2">TOURNAMENT <span class="gradient-text">AKTIF</span></h2>
                <p class="text-gray-500 text-sm">Pilih dan daftar turnamen yang sedang dibuka</p>
            </div>
            
            @if($tournaments->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($tournaments as $tournament)
                        <a href="{{ route('register', $tournament->slug) }}" class="bg-[#111] border border-gray-800 rounded-xl overflow-hidden hover:border-cyan-500/50 transition group cursor-pointer">
                            @if($tournament->banner_path)
                                <img src="{{ Storage::url($tournament->banner_path) }}" alt="{{ $tournament->title }}" class="w-full h-40 object-cover">
                            @else
                                <div class="w-full h-40 bg-gradient-to-br from-cyan-500/20 to-blue-600/20 flex items-center justify-center">
                                    <span class="text-4xl font-bold text-zinc-700">{{ strtoupper(substr($tournament->game_type, 0, 2)) }}</span>
                                </div>
                            @endif
                            
                            <div class="p-5">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-2 py-0.5 text-xs bg-cyan-500/10 text-cyan-400 rounded">{{ strtoupper($tournament->game_type) }}</span>
                                    @if($tournament->fee == 0)
                                        <span class="px-2 py-0.5 text-xs bg-green-500/10 text-green-400 rounded">GRATIS</span>
                                    @endif
                                </div>
                                <h3 class="text-lg font-semibold mb-2 group-hover:text-cyan-400 transition">{{ $tournament->title }}</h3>
                                <p class="text-sm text-zinc-400 mb-3">{{ $tournament->event_date->format('d M Y, H:i') }} WIB</p>
                                
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-zinc-500">{{ $tournament->participants_count }}/{{ $tournament->max_slots }} slot</span>
                                    @if($tournament->fee > 0)
                                        <span class="text-cyan-400 font-semibold">Rp {{ number_format($tournament->fee, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <div class="w-20 h-20 mx-auto mb-6 bg-[#111] rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <p class="text-xl text-gray-400 mb-2">Belum ada tournament aktif</p>
                    <p class="text-gray-600">Nantikan turnamen seru dari MIRAI Indonesia!</p>
                </div>
            @endif
        </div>
    </section>
    
    <!-- About Section -->
    <section id="about" class="py-24 px-6 bg-[#080808] border-b border-white/5">
        <div class="max-w-4xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-cyan-400 font-bold tracking-widest text-sm mb-2 uppercase">Tentang Kami</h2>
                    <h3 class="text-3xl md:text-4xl font-display font-bold text-white mb-6">MIRAI <span class="gradient-text">INDONESIA</span></h3>
                    
                    <p class="text-gray-400 mb-6 leading-relaxed">
                        <strong class="text-white">MIRAI</strong> (ミライ) berarti "Masa Depan" dalam bahasa Jepang. Kami adalah penyedia jasa Event Organizer dan Live Streaming profesional untuk turnamen esports.
                    </p>
                    <p class="text-gray-400 mb-8 leading-relaxed">
                        Dari turnamen komunitas hingga kompetisi besar, kami menghadirkan standar visual profesional (MPL / VCT) ke dalam setiap event.
                    </p>
                    
                    <a href="https://mirai-id.netlify.app/" target="_blank" class="inline-flex items-center gap-2 bg-gradient-to-r from-cyan-500 to-blue-600 text-white px-6 py-3 rounded font-display font-bold hover:opacity-90 transition shadow-[0_0_20px_rgba(6,182,212,0.4)] cursor-pointer">
                        Lihat Company Profile
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>
                
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-tr from-cyan-500/20 to-blue-500/20 blur-3xl rounded-full"></div>
                    <div class="relative bg-[#111] border border-gray-800 p-8 rounded-2xl grid grid-cols-2 gap-8 text-center">
                        <div>
                            <div class="text-4xl font-display font-bold text-white mb-1">50+</div>
                            <div class="text-xs text-gray-500 uppercase tracking-widest">Turnamen</div>
                        </div>
                        <div>
                            <div class="text-4xl font-display font-bold text-white mb-1">10k+</div>
                            <div class="text-xs text-gray-500 uppercase tracking-widest">Total Viewers</div>
                        </div>
                        <div>
                            <div class="text-4xl font-display font-bold text-white mb-1">100%</div>
                            <div class="text-xs text-gray-500 uppercase tracking-widest">On Time</div>
                        </div>
                        <div>
                            <div class="text-4xl font-display font-bold text-white mb-1">24 / 7</div>
                            <div class="text-xs text-gray-500 uppercase tracking-widest">Support</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="border-t border-white/5 py-8 px-4 bg-black/50">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <x-mirai-logo size="sm" />
            <div class="flex items-center gap-6 text-sm text-gray-500">
                <a href="https://mirai-id.netlify.app/" target="_blank" class="hover:text-cyan-400 transition cursor-pointer">Company Profile</a>
                <a href="https://wa.me/6281234567890" target="_blank" class="hover:text-cyan-400 transition cursor-pointer">Contact</a>
            </div>
            <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} MIRAI Hub. Powered by MIRAI Indonesia.</p>
        </div>
    </footer>
</body>
</html>
