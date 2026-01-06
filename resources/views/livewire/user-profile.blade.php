<div class="min-h-screen py-12 px-4">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-display font-bold mb-8">Profil Saya</h1>

        <!-- Profile Card -->
        <div class="bg-[#1a1a1a] border border-gray-800 rounded-xl p-6 mb-6">
            <h2 class="text-lg font-semibold mb-6 text-cyan-400">Informasi Profil</h2>

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-lg text-green-400 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form wire:submit="updateProfile" class="space-y-5">
                <!-- Avatar -->
                <div class="flex items-center gap-6 mb-6">
                    <div class="relative">
                        <div class="shrink-0 grow-0 rounded-full bg-[#222] border-2 border-gray-700 overflow-hidden"
                            style="width: 96px; height: 96px;">
                            @if ($newAvatar)
                                <img src="{{ $newAvatar->temporaryUrl() }}" class="object-cover"
                                    style="width: 96px; height: 96px;">
                            @elseif($this->avatarUrl)
                                <img src="{{ $this->avatarUrl }}" class="object-cover"
                                    style="width: 96px; height: 96px;">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <label
                            class="absolute bottom-0 right-0 w-8 h-8 bg-cyan-500 rounded-full flex items-center justify-center cursor-pointer hover:bg-cyan-600 transition">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <input type="file" wire:model="newAvatar" accept="image/*" class="hidden">
                        </label>
                    </div>
                    <div>
                        <p class="font-medium">{{ $name }}</p>
                        <p class="text-sm text-gray-500">{{ $email }}</p>
                        @if ($newAvatar)
                            <p class="text-xs text-cyan-400 mt-1">Foto baru dipilih</p>
                        @endif
                    </div>
                </div>
                @error('newAvatar')
                    <p class="text-red-400 text-sm">{{ $message }}</p>
                @enderror

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Nama Lengkap</label>
                    <input type="text" wire:model="name"
                        class="w-full px-3 py-4 mb-3 bg-[#222] border border-gray-700 rounded-lg focus:outline-none focus:border-cyan-500 transition">
                    @error('name')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email (readonly) -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Email</label>
                    <input type="email" value="{{ $email }}" disabled
                        class="w-full px-3 py-4 bg-[#111] border border-gray-800 rounded-lg text-gray-500 cursor-not-allowed">
                    <p class="text-xs text-gray-600 mt-1  mb-3">Email tidak dapat diubah</p>
                </div>

                <!-- WhatsApp -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">No. WhatsApp</label>
                    <input type="text" wire:model="whatsapp" placeholder="08xxxxxxxxxx"
                        class="w-full px-3 py-4 mb-4 bg-[#222] border border-gray-700 rounded-lg focus:outline-none focus:border-cyan-500 transition">
                    @error('whatsapp')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full py-3 bg-gradient-to-r from-cyan-500 to-blue-600 rounded-lg font-display font-bold hover:opacity-90 transition shadow-[0_0_20px_rgba(6,182,212,0.3)] cursor-pointer">
                    Simpan Perubahan
                </button>
            </form>
        </div>

        <!-- Game Profiles Card -->
        <div class="bg-[#1a1a1a] border border-gray-800 rounded-xl p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-cyan-400">Profil Game</h2>
                @if ($this->availableGames->count() > 0)
                    <button wire:click="openGameForm"
                        class="text-sm text-cyan-400 hover:text-cyan-300 transition flex items-center gap-1 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Game
                    </button>
                @endif
            </div>

            @if (session('game_success'))
                <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-lg text-green-400 text-sm">
                    {{ session('game_success') }}
                </div>
            @endif

            @if ($this->gameProfiles->count() > 0)
                <div class="space-y-3">
                    @foreach ($this->gameProfiles as $profile)
                        @php $gameName = \App\Models\UserGameProfile::getGameName($profile->game_type); @endphp
                        <div class="bg-[#111] border border-gray-800 rounded-lg p-4 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-lg bg-[#222] flex items-center justify-center text-xl font-bold text-cyan-400">
                                    {{ strtoupper(substr($profile->game_type, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-white">{{ $gameName }}</p>
                                    <p class="text-sm text-gray-400">{{ $profile->username }}</p>
                                    <p class="text-xs text-gray-500">
                                        ID: {{ $profile->game_id }}
                                        @if ($profile->server)
                                            • Server: {{ $profile->server }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                <button wire:click="openGameForm('{{ $profile->game_type }}')"
                                    class="p-2 text-gray-400 hover:text-cyan-400 transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <button wire:click="deleteGameProfile({{ $profile->id }})"
                                    wire:confirm="Hapus profil game ini?"
                                    class="p-2 text-gray-400 hover:text-red-400 transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 mx-auto mb-4 bg-[#111] rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                        </svg>
                    </div>
                    <p class="text-gray-400 mb-2">Belum ada profil game</p>
                    <p class="text-sm text-gray-600 mb-4">Tambahkan profil game untuk mempermudah pendaftaran turnamen
                    </p>
                    <button wire:click="openGameForm"
                        class="text-cyan-400 hover:text-cyan-300 text-sm font-medium cursor-pointer">
                        + Tambah Profil Game
                    </button>
                </div>
            @endif
        </div>

        <!-- Password Card -->
        <div class="bg-[#1a1a1a] border border-gray-800 rounded-xl p-6">
            <h2 class="text-lg font-semibold mb-6 text-cyan-400">Ubah Password</h2>

            @if (session('password_success'))
                <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-lg text-green-400 text-sm">
                    {{ session('password_success') }}
                </div>
            @endif

            @if (!auth()->user()->password)
                <p class="text-gray-500 text-sm mb-4">Kamu login dengan Google. Set password untuk bisa login dengan
                    email juga.</p>
            @endif

            <form wire:submit="updatePassword" class="space-y-5">
                @if (auth()->user()->password)
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Password Saat Ini</label>
                        <input type="password" wire:model="current_password"
                            class="w-full px-3 py-4 mb-3 bg-[#222] border border-gray-700 rounded-lg focus:outline-none focus:border-cyan-500 transition">
                        @error('current_password')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Password Baru</label>
                    <input type="password" wire:model="new_password"
                        class="w-full px-3 py-4 mb-3 bg-[#222] border border-gray-700 rounded-lg focus:outline-none focus:border-cyan-500 transition">
                    @error('new_password')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Konfirmasi Password Baru</label>
                    <input type="password" wire:model="new_password_confirmation"
                        class="w-full px-3 py-4 mb-6 bg-[#222] border border-gray-700 rounded-lg focus:outline-none focus:border-cyan-500 transition">
                </div>

                <button type="submit"
                    class="w-full py-3 bg-gray-700 hover:bg-gray-600 rounded-lg font-display font-bold transition cursor-pointer">
                    Ubah Password
                </button>
            </form>
        </div>

        <!-- Back Link -->
        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="text-gray-500 hover:text-cyan-400 transition cursor-pointer">
                ← Kembali ke Beranda
            </a>
        </div>
    </div>

    <!-- Game Profile Modal -->
    @if ($showGameForm)
        <div class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4"
            wire:click.self="closeGameForm">
            <div class="bg-[#1a1a1a] border border-gray-800 rounded-xl p-6 w-full max-w-md">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold">{{ $editingGameId ? 'Edit' : 'Tambah' }} Profil Game</h3>
                    <button wire:click="closeGameForm"
                        class="text-gray-500 hover:text-white transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="saveGameProfile" class="space-y-4">
                    <!-- Game Select -->
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Game</label>
                        @if ($editingGameId)
                            <input type="text" value="{{ $gameForm['game_type'] }}" disabled
                                class="w-full px-4 py-3 bg-[#111] border border-gray-800 rounded-lg text-gray-500 cursor-not-allowed">
                        @else
                            <select wire:model.live="gameForm.game_type"
                                class="w-full px-4 py-3 bg-[#222] border border-gray-700 rounded-lg focus:outline-none focus:border-cyan-500 transition">
                                <option value="">Pilih Game</option>
                                @foreach ($this->availableGames as $key => $game)
                                    <option value="{{ $key }}">{{ $game['name'] }}</option>
                                @endforeach
                            </select>
                        @endif
                        @error('gameForm.game_type')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Username / Nickname</label>
                        <input type="text" wire:model="gameForm.username" placeholder="Username in-game kamu"
                            class="w-full px-4 py-3 bg-[#222] border border-gray-700 rounded-lg focus:outline-none focus:border-cyan-500 transition">
                        @error('gameForm.username')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Game ID -->
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">ID Game</label>
                        <input type="text" wire:model="gameForm.game_id"
                            placeholder="{{ isset($this->supportedGames[$gameForm['game_type']]) ? $this->supportedGames[$gameForm['game_type']]['id_placeholder'] : 'ID Game kamu' }}"
                            class="w-full px-4 py-3 bg-[#222] border border-gray-700 rounded-lg focus:outline-none focus:border-cyan-500 transition">
                        @error('gameForm.game_id')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Server (conditional) -->
                    @if (isset($this->supportedGames[$gameForm['game_type']]) && $this->supportedGames[$gameForm['game_type']]['has_server'])
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Server</label>
                            <input type="text" wire:model="gameForm.server"
                                placeholder="{{ $this->supportedGames[$gameForm['game_type']]['server_placeholder'] ?? 'Server ID' }}"
                                class="w-full px-4 py-3 bg-[#222] border border-gray-700 rounded-lg focus:outline-none focus:border-cyan-500 transition">
                        </div>
                    @endif

                    <div class="flex gap-3 pt-2">
                        <button type="button" wire:click="closeGameForm"
                            class="flex-1 py-3 bg-gray-700 hover:bg-gray-600 rounded-lg font-medium transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 rounded-lg font-display font-bold hover:opacity-90 transition cursor-pointer">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
