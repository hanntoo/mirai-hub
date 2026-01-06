@php use Illuminate\Support\Facades\Storage; @endphp
<div>
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.tournaments.index') }}" class="p-2 text-zinc-400 hover:text-zinc-100 transition cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold">Peserta</h1>
                <p class="text-zinc-400">{{ $tournament->title }}</p>
            </div>
        </div>
        <a href="{{ route('admin.tournaments.export', $tournament) }}" 
           class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg font-medium transition flex items-center gap-2 cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export Excel
        </a>
    </div>
    
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
            <p class="text-zinc-400 text-sm">Total Peserta</p>
            <p class="text-2xl font-bold">{{ $tournament->participants()->count() }}</p>
        </div>
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
            <p class="text-zinc-400 text-sm">Slot Tersisa</p>
            <p class="text-2xl font-bold">{{ $tournament->max_slots - $tournament->participants()->count() }}</p>
        </div>
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
            <p class="text-yellow-400 text-sm">Pending</p>
            <p class="text-2xl font-bold">{{ $tournament->participants()->where('payment_status', 'pending')->count() }}</p>
        </div>
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
            <p class="text-green-400 text-sm">Verified</p>
            <p class="text-2xl font-bold">{{ $tournament->participants()->where('payment_status', 'verified')->count() }}</p>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 mb-6">
        <div class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari tim, kapten, atau WhatsApp..." 
                       class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg focus:outline-none focus:border-cyan-500 transition">
            </div>
            <select wire:model.live="statusFilter" class="px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg focus:outline-none focus:border-cyan-500">
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="verified">Verified</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </div>
    
    <!-- Participants Table -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
        <table class="w-full">
            <thead class="bg-zinc-800/50">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-medium text-zinc-400">#</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-zinc-400">Tim</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-zinc-400">Kapten</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-zinc-400">WhatsApp</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-zinc-400">Tanggal Daftar</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-zinc-400">Status</th>
                    <th class="px-6 py-4 text-right text-sm font-medium text-zinc-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @forelse($participants as $index => $participant)
                    <tr class="hover:bg-zinc-800/30 transition">
                        <td class="px-6 py-4 text-zinc-500">{{ $participants->firstItem() + $index }}</td>
                        <td class="px-6 py-4 font-medium">{{ $participant->team_name }}</td>
                        <td class="px-6 py-4 text-zinc-300">{{ $participant->captain_name }}</td>
                        <td class="px-6 py-4 text-zinc-300">{{ $participant->whatsapp }}</td>
                        <td class="px-6 py-4 text-zinc-300">{{ $participant->registered_at?->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $participant->payment_status === 'verified' ? 'bg-green-500/10 text-green-400' : 
                                   ($participant->payment_status === 'pending' ? 'bg-yellow-500/10 text-yellow-400' : 'bg-red-500/10 text-red-400') }}">
                                {{ ucfirst($participant->payment_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="viewDetail({{ $participant->id }})" 
                                        class="p-2 text-zinc-400 hover:text-cyan-400 transition cursor-pointer" title="Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                                @if($participant->payment_status === 'pending')
                                    <button wire:click="updateStatus({{ $participant->id }}, 'verified')" 
                                            class="p-2 text-zinc-400 hover:text-green-400 transition cursor-pointer" title="Verify">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-zinc-500">
                            Belum ada peserta terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-6">
        {{ $participants->links() }}
    </div>
    
    <!-- Detail Modal -->
    @if($showDetailModal && $selectedParticipant)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data x-init="document.body.classList.add('overflow-hidden')" x-on:close-modal.window="$wire.closeModal()">
            <div class="fixed inset-0 bg-black/70" wire:click="closeModal"></div>
            <div class="relative bg-zinc-900 border border-zinc-800 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-zinc-900 border-b border-zinc-800 p-6 flex items-center justify-between">
                    <h2 class="text-xl font-bold">Detail Peserta</h2>
                    <button wire:click="closeModal" class="p-2 text-zinc-400 hover:text-zinc-100 transition cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Basic Info -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-zinc-400">Nama Tim</label>
                            <p class="font-medium">{{ $selectedParticipant->team_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-zinc-400">Nama Kapten</label>
                            <p class="font-medium">{{ $selectedParticipant->captain_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-zinc-400">WhatsApp</label>
                            <p class="font-medium">{{ $selectedParticipant->whatsapp }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-zinc-400">Tanggal Daftar</label>
                            <p class="font-medium">{{ $selectedParticipant->registered_at?->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label class="text-sm text-zinc-400 block mb-2">Status Pembayaran</label>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1.5 text-sm rounded-full 
                                {{ $selectedParticipant->payment_status === 'verified' ? 'bg-green-500/10 text-green-400' : 
                                   ($selectedParticipant->payment_status === 'pending' ? 'bg-yellow-500/10 text-yellow-400' : 'bg-red-500/10 text-red-400') }}">
                                {{ ucfirst($selectedParticipant->payment_status) }}
                            </span>
                            
                            @if($selectedParticipant->payment_status !== 'verified')
                                <button wire:click="updateStatus({{ $selectedParticipant->id }}, 'verified')" 
                                        class="px-3 py-1.5 text-sm bg-green-600 hover:bg-green-700 rounded-lg transition cursor-pointer">
                                    Verify
                                </button>
                            @endif
                            @if($selectedParticipant->payment_status !== 'rejected')
                                <button wire:click="updateStatus({{ $selectedParticipant->id }}, 'rejected')" 
                                        class="px-3 py-1.5 text-sm bg-red-600 hover:bg-red-700 rounded-lg transition cursor-pointer">
                                    Reject
                                </button>
                            @endif
                            @if($selectedParticipant->payment_status !== 'pending')
                                <button wire:click="updateStatus({{ $selectedParticipant->id }}, 'pending')" 
                                        class="px-3 py-1.5 text-sm bg-yellow-600 hover:bg-yellow-700 rounded-lg transition cursor-pointer">
                                    Set Pending
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Dynamic Fields -->
                    @if($selectedParticipant->submission_data && count($selectedParticipant->submission_data) > 0)
                        <div>
                            <h3 class="text-lg font-semibold mb-4 border-b border-zinc-800 pb-2">Data Tambahan</h3>
                            <div class="space-y-4">
                                @foreach($tournament->form_schema ?? [] as $field)
                                    @if(!in_array($field['type'], ['image_block', 'text_block', 'link_block']))
                                        @php
                                            $value = $selectedParticipant->submission_data[$field['id']] ?? null;
                                        @endphp
                                        <div>
                                            <label class="text-sm text-zinc-400">{{ $field['label'] ?? $field['id'] }}</label>
                                            @if($field['type'] === 'file' && $value)
                                                <div class="mt-1">
                                                    @php
                                                        $extension = pathinfo($value, PATHINFO_EXTENSION);
                                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                    @endphp
                                                    @if($isImage)
                                                        <img src="{{ Storage::url($value) }}" alt="Uploaded file" class="max-w-full h-auto max-h-64 rounded-lg">
                                                    @else
                                                        <a href="{{ Storage::url($value) }}" target="_blank" 
                                                           class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-800 rounded-lg hover:bg-zinc-700 transition cursor-pointer">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                            </svg>
                                                            Lihat File
                                                        </a>
                                                    @endif
                                                </div>
                                            @elseif(is_array($value))
                                                <p class="font-medium">{{ implode(', ', $value) }}</p>
                                            @else
                                                <p class="font-medium">{{ $value ?: '-' }}</p>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="sticky bottom-0 bg-zinc-900 border-t border-zinc-800 p-6 flex justify-between">
                    <button wire:click="deleteParticipant({{ $selectedParticipant->id }})" 
                            wire:confirm="Yakin ingin menghapus peserta ini?"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg transition cursor-pointer">
                        Hapus Peserta
                    </button>
                    <button wire:click="closeModal" class="px-4 py-2 bg-zinc-700 hover:bg-zinc-600 rounded-lg transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
