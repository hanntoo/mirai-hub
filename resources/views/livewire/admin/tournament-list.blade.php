<div>
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold">Tournaments</h1>
        <a href="{{ route('admin.tournaments.create') }}" class="px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 rounded-lg font-medium hover:opacity-90 transition cursor-pointer">
            + Buat Tournament
        </a>
    </div>
    
    <!-- Filters -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 mb-6">
        <div class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari tournament..." 
                       class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg focus:outline-none focus:border-cyan-500 transition">
            </div>
            <select wire:model.live="statusFilter" class="px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg focus:outline-none focus:border-cyan-500">
                <option value="">Semua Status</option>
                <option value="draft">Draft</option>
                <option value="open">Open</option>
                <option value="closed">Closed</option>
            </select>
        </div>
    </div>
    
    <!-- Tournament Table -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
        <table class="w-full">
            <thead class="bg-zinc-800/50">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-medium text-zinc-400">Tournament</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-zinc-400">Game</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-zinc-400">Tanggal</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-zinc-400">Peserta</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-zinc-400">Status</th>
                    <th class="px-6 py-4 text-right text-sm font-medium text-zinc-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @forelse($tournaments as $tournament)
                    <tr class="hover:bg-zinc-800/30 transition">
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium">{{ $tournament->title }}</p>
                                <p class="text-sm text-zinc-500">{{ $tournament->slug }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-zinc-300">{{ strtoupper($tournament->game_type) }}</td>
                        <td class="px-6 py-4 text-zinc-300">{{ $tournament->event_date->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4">
                            <span class="text-zinc-300">{{ $tournament->participants_count }}</span>
                            <span class="text-zinc-500">/{{ $tournament->max_slots }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <button wire:click="toggleStatus({{ $tournament->id }})" 
                                    class="px-2 py-1 text-xs rounded-full cursor-pointer hover:opacity-80 transition
                                    {{ $tournament->status === 'open' ? 'bg-green-500/10 text-green-400' : ($tournament->status === 'draft' ? 'bg-zinc-500/10 text-zinc-400' : 'bg-red-500/10 text-red-400') }}">
                                {{ ucfirst($tournament->status) }}
                            </button>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('register', $tournament->slug) }}" target="_blank" 
                                   class="p-2 text-zinc-400 hover:text-cyan-400 transition cursor-pointer" title="Preview">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.tournaments.participants', $tournament) }}" 
                                   class="p-2 text-zinc-400 hover:text-blue-400 transition cursor-pointer" title="Peserta">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.tournaments.edit', $tournament) }}" 
                                   class="p-2 text-zinc-400 hover:text-yellow-400 transition cursor-pointer" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <button wire:click="deleteTournament({{ $tournament->id }})" 
                                        wire:confirm="Yakin ingin menghapus tournament ini?"
                                        class="p-2 text-zinc-400 hover:text-red-400 transition cursor-pointer" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-zinc-500">
                            Belum ada tournament. <a href="{{ route('admin.tournaments.create') }}" class="text-cyan-400 hover:underline cursor-pointer">Buat sekarang</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-6">
        {{ $tournaments->links() }}
    </div>
</div>
