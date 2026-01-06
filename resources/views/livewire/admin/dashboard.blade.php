<div>
    <h1 class="text-3xl font-bold mb-8">Dashboard</h1>
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-cyan-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div>
                    <p class="text-zinc-400 text-sm">Total Tournaments</p>
                    <p class="text-2xl font-bold">{{ $stats['total_tournaments'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-zinc-400 text-sm">Open Tournaments</p>
                    <p class="text-2xl font-bold">{{ $stats['open_tournaments'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-zinc-400 text-sm">Total Peserta</p>
                    <p class="text-2xl font-bold">{{ $stats['total_participants'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-zinc-400 text-sm">Pending Payments</p>
                    <p class="text-2xl font-bold">{{ $stats['pending_payments'] }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Tournaments -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl">
            <div class="p-6 border-b border-zinc-800 flex items-center justify-between">
                <h2 class="text-lg font-semibold">Tournament Terbaru</h2>
                <a href="{{ route('admin.tournaments.index') }}" class="text-cyan-400 text-sm hover:underline cursor-pointer">Lihat Semua</a>
            </div>
            <div class="divide-y divide-zinc-800">
                @forelse($recentTournaments as $tournament)
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <p class="font-medium">{{ $tournament->title }}</p>
                            <p class="text-sm text-zinc-400">{{ $tournament->game_type }} • {{ $tournament->participants_count }} peserta</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full {{ $tournament->status === 'open' ? 'bg-green-500/10 text-green-400' : ($tournament->status === 'draft' ? 'bg-zinc-500/10 text-zinc-400' : 'bg-red-500/10 text-red-400') }}">
                            {{ ucfirst($tournament->status) }}
                        </span>
                    </div>
                @empty
                    <div class="p-8 text-center text-zinc-500">
                        Belum ada tournament
                    </div>
                @endforelse
            </div>
        </div>
        
        <!-- Recent Participants -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl">
            <div class="p-6 border-b border-zinc-800">
                <h2 class="text-lg font-semibold">Pendaftaran Terbaru</h2>
            </div>
            <div class="divide-y divide-zinc-800">
                @forelse($recentParticipants as $participant)
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <p class="font-medium">{{ $participant->team_name }}</p>
                            <p class="text-sm text-zinc-400">{{ $participant->tournament->title }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full {{ $participant->payment_status === 'verified' ? 'bg-green-500/10 text-green-400' : ($participant->payment_status === 'pending' ? 'bg-yellow-500/10 text-yellow-400' : 'bg-red-500/10 text-red-400') }}">
                            {{ ucfirst($participant->payment_status) }}
                        </span>
                    </div>
                @empty
                    <div class="p-8 text-center text-zinc-500">
                        Belum ada pendaftaran
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
