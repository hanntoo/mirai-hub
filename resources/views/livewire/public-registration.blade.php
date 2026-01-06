@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp
<div class="max-w-xl mx-auto py-10 px-4 pb-32" x-data="registrationForm()" x-init="loadSavedData()">
    @if ($submitted)
        {{-- Success Message --}}
        <div class="bg-[#1a1a1a] border-t-8 border-green-500 rounded-xl p-8 shadow-2xl text-center animate-fade-in">
            <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">Terima Kasih!</h2>
            <p class="text-gray-400 mb-6">Pendaftaran Anda telah berhasil direkam.</p>

            @if ($tournament->fee > 0)
                <div class="bg-black/30 border border-gray-800 rounded-lg p-4 text-left mb-6">
                    <h3 class="font-semibold text-white mb-3">Langkah Selanjutnya:</h3>
                    <ol class="list-decimal list-inside space-y-2 text-gray-400 text-sm">
                        <li>Lakukan pembayaran sebesar <span class="text-cyan-400 font-semibold">Rp
                                {{ number_format($tournament->fee, 0, ',', '.') }}</span></li>
                        <li>Tunggu konfirmasi dari panitia via WhatsApp</li>
                        <li>Siapkan tim kamu untuk bertanding!</li>
                    </ol>
                </div>
            @endif

            <a href="/" class="text-cyan-500 hover:underline cursor-pointer">← Kembali ke Beranda</a>
        </div>
    @else
        {{-- Check if tournament is closed --}}
        @if ($tournament->status === 'closed')
            <div class="bg-[#1a1a1a] border-t-8 border-red-500 rounded-xl p-8 shadow-2xl text-center">
                <svg class="w-12 h-12 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <h2 class="text-2xl font-bold text-white mb-2">Pendaftaran Ditutup</h2>
                <p class="text-gray-400 mb-6">Maaf, kuota sudah penuh atau waktu pendaftaran telah habis.</p>
                <a href="/" class="text-cyan-500 hover:underline cursor-pointer">Kembali</a>
            </div>
        @elseif($tournament->isFull())
            <div class="bg-[#1a1a1a] border-t-8 border-red-500 rounded-xl p-8 shadow-2xl text-center">
                <svg class="w-12 h-12 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h2 class="text-xl font-bold text-red-400 mb-2">Slot Penuh!</h2>
                <p class="text-gray-400">Maaf, pendaftaran untuk tournament ini sudah penuh.</p>
            </div>
        @else
            {{-- Tournament Header Card --}}
            <div class="bg-[#1a1a1a] border-t-8 border-cyan-500 rounded-xl p-6 mb-6 shadow-2xl">
                <h1 class="text-3xl font-bold text-white mb-2">{{ $tournament->title }}</h1>
                <p class="text-gray-400 text-sm mb-4 whitespace-pre-wrap">
                    {{ $tournament->description ?: 'Silakan isi data dengan benar.' }}</p>
                <div class="flex flex-wrap gap-4 text-xs border-t border-gray-800 pt-4">
                    <span class="flex items-center gap-1 text-cyan-400">
                        <x-lucide-gamepad-2 class="w-3 h-3" />
                        {{ $tournament->game?->abbreviation ?? strtoupper($tournament->game_type) }}
                    </span>
                    <span class="flex items-center gap-1 text-cyan-400">
                        <x-lucide-calendar class="w-3 h-3" />
                        {{ $tournament->event_date->format('d M Y, H:i') }}
                    </span>
                    @if ($tournament->fee > 0)
                        <span class="flex items-center gap-1 text-cyan-400">
                            <x-lucide-wallet class="w-3 h-3" />
                            Rp {{ number_format($tournament->fee, 0, ',', '.') }}
                        </span>
                    @else
                        <span class="flex items-center gap-1 text-green-400">
                            <x-lucide-wallet class="w-3 h-3" />
                            GRATIS
                        </span>
                    @endif
                    <span class="text-red-500 font-bold ml-auto">* Wajib</span>
                </div>
            </div>

            {{-- Slot Progress --}}
            <div class="mb-6 flex items-center gap-4">
                <div class="flex-1 bg-gray-800 rounded-full h-2 overflow-hidden">
                    @php $percentage = ($tournament->participants()->count() / $tournament->max_slots) * 100; @endphp
                    <div class="h-full bg-gradient-to-r from-cyan-500 to-blue-600" style="width: {{ $percentage }}%">
                    </div>
                </div>
                <span
                    class="text-xs text-gray-400">{{ $tournament->participants()->count() }}/{{ $tournament->max_slots }}
                    slot</span>
            </div>

            {{-- Game Profile Info --}}
            @if ($this->gameProfile)
                @php $gameName = \App\Models\UserGameProfile::getGameName($tournament->game_type); @endphp
                <div class="mb-6 bg-cyan-950/30 border border-cyan-500/30 rounded-xl p-4" x-data="gameProfileCopy()">
                    <div class="flex items-center gap-3 mb-3">
                        <div
                            class="w-10 h-10 rounded-lg bg-cyan-500/20 flex items-center justify-center text-lg font-bold text-cyan-400">
                            {{ $tournament->game?->abbreviation ?? strtoupper(substr($tournament->game_type, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-xs text-cyan-400 font-medium">Profil Game Kamu ({{ $gameName }})</p>
                            <p class="text-white font-semibold">{{ $this->gameProfile->username }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 text-xs">
                        <button type="button"
                            @click="copyToClipboard('{{ addslashes($this->gameProfile->username) }}', 'username')"
                            class="flex items-center gap-2 px-3 py-2 bg-black/30 border border-gray-700 rounded-lg hover:border-cyan-500 transition cursor-pointer">
                            <span class="text-gray-400">Username:</span>
                            <span class="text-white font-mono">{{ $this->gameProfile->username }}</span>
                            <x-lucide-copy class="w-3 h-3 text-gray-500" x-show="copied !== 'username'" />
                            <x-lucide-check class="w-3 h-3 text-green-400" x-show="copied === 'username'" x-cloak />
                        </button>
                        <button type="button"
                            @click="copyToClipboard('{{ addslashes($this->gameProfile->game_id) }}', 'id')"
                            class="flex items-center gap-2 px-3 py-2 bg-black/30 border border-gray-700 rounded-lg hover:border-cyan-500 transition cursor-pointer">
                            <span class="text-gray-400">ID:</span>
                            <span class="text-white font-mono">{{ $this->gameProfile->game_id }}</span>
                            <x-lucide-copy class="w-3 h-3 text-gray-500" x-show="copied !== 'id'" />
                            <x-lucide-check class="w-3 h-3 text-green-400" x-show="copied === 'id'" x-cloak />
                        </button>
                        @if ($this->gameProfile->server)
                            <button type="button"
                                @click="copyToClipboard('{{ addslashes($this->gameProfile->server) }}', 'server')"
                                class="flex items-center gap-2 px-3 py-2 bg-black/30 border border-gray-700 rounded-lg hover:border-cyan-500 transition cursor-pointer">
                                <span class="text-gray-400">Server:</span>
                                <span class="text-white font-mono">{{ $this->gameProfile->server }}</span>
                                <x-lucide-copy class="w-3 h-3 text-gray-500" x-show="copied !== 'server'" />
                                <x-lucide-check class="w-3 h-3 text-green-400" x-show="copied === 'server'" x-cloak />
                            </button>
                        @endif
                    </div>
                </div>

                <script>
                    function gameProfileCopy() {
                        return {
                            copied: null,
                            copyToClipboard(text, key) {
                                const self = this;

                                // Try modern clipboard API first
                                if (navigator.clipboard && window.isSecureContext) {
                                    navigator.clipboard.writeText(text).then(function() {
                                        self.copied = key;
                                        setTimeout(function() {
                                            self.copied = null;
                                        }, 2000);
                                    }).catch(function() {
                                        self.fallbackCopy(text, key);
                                    });
                                } else {
                                    self.fallbackCopy(text, key);
                                }
                            },
                            fallbackCopy(text, key) {
                                const self = this;
                                const textarea = document.createElement('textarea');
                                textarea.value = text;
                                textarea.style.position = 'fixed';
                                textarea.style.left = '-9999px';
                                textarea.style.top = '0';
                                document.body.appendChild(textarea);
                                textarea.focus();
                                textarea.select();

                                try {
                                    document.execCommand('copy');
                                    self.copied = key;
                                    setTimeout(function() {
                                        self.copied = null;
                                    }, 2000);
                                } catch (err) {
                                    console.error('Copy failed:', err);
                                    alert('Gagal menyalin. Silakan copy manual.');
                                }

                                document.body.removeChild(textarea);
                            }
                        }
                    }
                </script>
            @else
                @php $gameName = \App\Models\UserGameProfile::getGameName($tournament->game_type); @endphp
                <div class="mb-6 bg-yellow-950/30 border border-yellow-500/30 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <p class="text-sm text-yellow-400 font-medium">Belum ada profil {{ $gameName }}</p>
                            <p class="text-xs text-gray-400 mt-1">Tambahkan profil game di <a
                                    href="{{ route('profile') }}"
                                    class="text-cyan-400 hover:underline cursor-pointer">halaman profil</a> untuk
                                mempermudah pendaftaran.</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Step Indicator (only show if multiple sections) --}}
            @if ($this->totalSteps > 1)
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs text-gray-500">Bagian {{ $currentStep + 1 }} dari
                            {{ $this->totalSteps }}</span>
                        <span class="text-xs text-cyan-400 font-medium">{{ $currentSection['title'] ?? '' }}</span>
                    </div>
                    <div class="flex gap-1">
                        @for ($i = 0; $i < $this->totalSteps; $i++)
                            <div
                                class="flex-1 h-1 rounded-full transition-all {{ $i <= $currentStep ? 'bg-cyan-500' : 'bg-gray-700' }}">
                            </div>
                        @endfor
                    </div>
                </div>
            @endif

            {{-- Registration Form --}}
            <form wire:submit="{{ $currentStep === $this->totalSteps - 1 ? 'submit' : 'nextStep' }}"
                class="space-y-4">
                @if (session('error'))
                    <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Section Header (if multi-step) --}}
                @if ($this->totalSteps > 1 && $currentSection)
                    <div
                        class="bg-gradient-to-r from-cyan-900/20 to-blue-900/20 border border-cyan-500/30 rounded-xl p-4 mb-4">
                        <h2 class="text-lg font-bold text-cyan-400">{{ $currentSection['title'] }}</h2>
                        @if ($currentSection['description'])
                            <p class="text-sm text-gray-400 mt-1">{{ $currentSection['description'] }}</p>
                        @endif
                    </div>
                @endif

                {{-- Fixed Fields: Team Info (only on first section) --}}
                @if ($currentSection['includesFixedFields'] ?? false)
                    <div class="bg-[#1a1a1a] border border-gray-800 rounded-xl p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-white mb-1">Nama Tim <span
                                    class="text-red-500">*</span></label>
                            <input type="text" wire:model="team_name" placeholder="Contoh: Team Phoenix"
                                class="w-full bg-black/50 border border-gray-700 rounded p-3 text-white text-sm outline-none focus:border-cyan-500 transition">
                            @error('team_name')
                                <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-white mb-1">Nama Kapten <span
                                    class="text-red-500">*</span></label>
                            <input type="text" wire:model="captain_name" placeholder="Nama lengkap kapten tim"
                                class="w-full bg-black/50 border border-gray-700 rounded p-3 text-white text-sm outline-none focus:border-cyan-500 transition">
                            @error('captain_name')
                                <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-white mb-1">WhatsApp Kapten <span
                                    class="text-red-500">*</span></label>
                            <input type="tel" wire:model="whatsapp" placeholder="08xxxxxxxxxx"
                                class="w-full bg-black/50 border border-gray-700 rounded p-3 text-white text-sm outline-none focus:border-cyan-500 transition">
                            @error('whatsapp')
                                <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endif

                {{-- Dynamic Fields for Current Section --}}
                @foreach ($currentSection['fields'] ?? [] as $field)
                    <div wire:key="field-{{ $field['id'] }}">
                        @switch($field['type'])
                            {{-- STATIC BLOCKS --}}
                            @case('note')
                                <div class="bg-[#1a1a1a] border border-gray-800 rounded-xl p-6">
                                    <h3 class="text-lg font-bold text-white">{{ $field['label'] }}</h3>
                                    @if ($field['description'] ?? false)
                                        <p class="text-sm text-gray-400 whitespace-pre-wrap mt-2">{{ $field['description'] }}
                                        </p>
                                    @endif
                                </div>
                            @break

                            @case('image-view')
                                <div class="bg-[#1a1a1a] border border-gray-800 rounded-xl p-2 text-center">
                                    @if ($field['label'] ?? false)
                                        <p class="text-sm text-gray-400 mb-2">{{ $field['label'] }}</p>
                                    @endif
                                    @if ($field['description'] ?? false)
                                        @php
                                            $imagePath = $field['description'];
                                            $imageSrc = Str::startsWith($imagePath, ['http://', 'https://'])
                                                ? $imagePath
                                                : Storage::url($imagePath);
                                        @endphp
                                        <div class="relative group" x-data="{ showFullscreen: false }">
                                            <img src="{{ $imageSrc }}" class="w-full rounded-lg cursor-pointer"
                                                alt="{{ $field['label'] ?? 'Image' }}" onerror="this.style.display='none'"
                                                @click="showFullscreen = true">
                                            <div
                                                class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition">
                                                <button type="button" @click="showFullscreen = true"
                                                    class="p-2 bg-black/70 rounded-lg text-white hover:bg-black transition cursor-pointer"
                                                    title="Fullscreen">
                                                    <x-lucide-maximize class="w-4 h-4" />
                                                </button>
                                            </div>

                                            {{-- Fullscreen Modal --}}
                                            <template x-teleport="body">
                                                <div x-show="showFullscreen" x-cloak
                                                    x-transition:enter="transition ease-out duration-200"
                                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                                    x-transition:leave="transition ease-in duration-150"
                                                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                                    @click="showFullscreen = false"
                                                    @keydown.escape.window="showFullscreen = false"
                                                    class="fixed inset-0 z-[99999] bg-black flex items-center justify-center p-4 cursor-pointer">
                                                    <div
                                                        class="absolute top-4 left-1/2 -translate-x-1/2 flex gap-2 bg-black/80 backdrop-blur-sm px-3 py-2 rounded-full border border-gray-700">
                                                        <button type="button"
                                                            @click.stop="
                                                            const w = window.open('', '_blank');
                                                            w.document.write('<html><head><title>Preview</title><style>body{margin:0;background:#000;display:flex;justify-content:center;align-items:center;min-height:100vh;}img{max-width:100%;max-height:100vh;object-fit:contain;}</style></head><body><img src=\'{{ $imageSrc }}\'/></body></html>');
                                                            w.document.close();
                                                        "
                                                            class="p-2 text-white hover:text-cyan-400 transition cursor-pointer"
                                                            title="Buka di tab baru">
                                                            <x-lucide-external-link class="w-5 h-5" />
                                                        </button>
                                                        <div class="w-px bg-gray-600"></div>
                                                        <button type="button" @click="showFullscreen = false"
                                                            class="p-2 text-white hover:text-red-400 transition cursor-pointer"
                                                            title="Tutup (ESC)">
                                                            <x-lucide-x class="w-5 h-5" />
                                                        </button>
                                                    </div>
                                                    <img src="{{ $imageSrc }}"
                                                        class="max-w-full max-h-[90vh] object-contain cursor-default"
                                                        @click.stop>
                                                </div>
                                            </template>
                                        </div>
                                    @endif
                                </div>
                            @break

                            @case('link')
                                <a href="{{ $field['description'] ?? '#' }}" target="_blank" rel="noopener"
                                    class="bg-[#1a1a1a] border border-gray-800 rounded-xl p-6 flex items-center gap-4 hover:border-cyan-500 cursor-pointer block transition">
                                    <svg class="w-5 h-5 text-cyan-500 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                    <span
                                        class="font-bold text-cyan-400">{{ $field['label'] ?: $field['description'] }}</span>
                                </a>
                            @break

                            {{-- INPUT FIELDS --}}

                            @default
                                @php $hasError = $errors->has("answers.{$field['id']}") || $errors->has("fileUploads.{$field['id']}"); @endphp
                                <div
                                    class="bg-[#1a1a1a] border {{ $hasError ? 'border-red-500' : 'border-gray-800' }} rounded-xl p-6 transition focus-within:border-cyan-500">
                                    <label class="block text-sm font-medium text-white mb-1">
                                        {{ $field['label'] ?? 'Field' }}
                                        @if ($field['required'] ?? false)
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>

                                    {{-- Media Embed: Image (Independent) --}}
                                    @if ($field['imageUrl'] ?? false)
                                        @php
                                            $embedPath = $field['imageUrl'];
                                            $embedSrc = Str::startsWith($embedPath, ['http://', 'https://'])
                                                ? $embedPath
                                                : Storage::url($embedPath);
                                        @endphp
                                        <div class="relative group mb-3" x-data="{ showFullscreen: false }">
                                            <img src="{{ $embedSrc }}"
                                                class="w-full max-h-60 object-contain rounded border border-gray-700 bg-black/30 cursor-pointer"
                                                alt="" @click="showFullscreen = true">
                                            <div
                                                class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition">
                                                <button type="button" @click="showFullscreen = true"
                                                    class="p-2 bg-black/70 rounded-lg text-white hover:bg-black transition cursor-pointer"
                                                    title="Fullscreen">
                                                    <x-lucide-maximize class="w-4 h-4" />
                                                </button>
                                            </div>

                                            {{-- Fullscreen Modal --}}
                                            <template x-teleport="body">
                                                <div x-show="showFullscreen" x-cloak
                                                    x-transition:enter="transition ease-out duration-200"
                                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                                    x-transition:leave="transition ease-in duration-150"
                                                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                                    @click="showFullscreen = false"
                                                    @keydown.escape.window="showFullscreen = false"
                                                    class="fixed inset-0 z-[99999] bg-black flex items-center justify-center p-4 cursor-pointer">
                                                    <div
                                                        class="absolute top-4 left-1/2 -translate-x-1/2 flex gap-2 bg-black/80 backdrop-blur-sm px-3 py-2 rounded-full border border-gray-700">
                                                        <button type="button"
                                                            @click.stop="
                                                            const w = window.open('', '_blank');
                                                            w.document.write('<html><head><title>Preview</title><style>body{margin:0;background:#000;display:flex;justify-content:center;align-items:center;min-height:100vh;}img{max-width:100%;max-height:100vh;object-fit:contain;}</style></head><body><img src=\'{{ $embedSrc }}\'/></body></html>');
                                                            w.document.close();
                                                        "
                                                            class="p-2 text-white hover:text-cyan-400 transition cursor-pointer"
                                                            title="Buka di tab baru">
                                                            <x-lucide-external-link class="w-5 h-5" />
                                                        </button>
                                                        <div class="w-px bg-gray-600"></div>
                                                        <button type="button" @click="showFullscreen = false"
                                                            class="p-2 text-white hover:text-red-400 transition cursor-pointer"
                                                            title="Tutup (ESC)">
                                                            <x-lucide-x class="w-5 h-5" />
                                                        </button>
                                                    </div>
                                                    <img src="{{ $embedSrc }}"
                                                        class="max-w-full max-h-[90vh] object-contain cursor-default"
                                                        @click.stop>
                                                </div>
                                            </template>
                                        </div>
                                    @endif

                                    {{-- Media Embed: Link (Independent) --}}
                                    @if ($field['linkUrl'] ?? false)
                                        <a href="{{ $field['linkUrl'] }}" target="_blank" rel="noopener"
                                            class="inline-flex items-center gap-2 text-xs text-cyan-400 mb-3 hover:underline border border-cyan-900 px-3 py-2 rounded bg-cyan-950/20 w-full cursor-pointer">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                            </svg>
                                            {{ $field['linkText'] ?: $field['linkUrl'] }}
                                        </a>
                                    @endif

                                    {{-- Description --}}
                                    @if ($field['description'] ?? false)
                                        <p class="text-xs text-gray-500 mb-3 whitespace-pre-wrap">{{ $field['description'] }}
                                        </p>
                                    @endif

                                    @include('livewire.partials.form-field-input', ['field' => $field])
                                </div>
                        @endswitch
                    </div>
                @endforeach

                {{-- Navigation Buttons --}}
                <div class="flex justify-between items-center mt-8 gap-4">
                    @if ($currentStep > 0)
                        <button type="button" wire:click="previousStep"
                            class="px-6 py-3 border border-gray-700 rounded-lg text-gray-300 hover:text-white hover:border-gray-500 transition flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Sebelumnya
                        </button>
                    @else
                        <div></div>
                    @endif

                    @if ($currentStep < $this->totalSteps - 1)
                        <button type="submit"
                            class="px-8 py-3 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-lg shadow-lg transition flex items-center gap-2 cursor-pointer"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove>Selanjutnya</span>
                            <span wire:loading>Memproses...</span>
                            <svg class="w-4 h-4" wire:loading.remove fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    @else
                        <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold rounded-lg shadow-lg transition flex items-center gap-2 disabled:opacity-50 cursor-pointer"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove>Kirim Pendaftaran</span>
                            <span wire:loading class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Memproses...
                            </span>
                        </button>
                    @endif
                </div>

                {{-- Autosave indicator --}}
                <div class="text-center mt-4" x-show="lastSaved" x-cloak>
                    <span class="text-xs text-gray-500 flex items-center justify-center gap-1">
                        <svg class="w-3 h-3 text-green-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        Progress tersimpan otomatis
                    </span>
                </div>
            </form>

            <div class="text-center mt-12 text-[10px] text-gray-600">&copy; {{ date('Y') }} MIRAI Hub. Powered by
                MIRAI Indonesia.</div>
        @endif
    @endif
</div>

<script>
    function registrationForm() {
        return {
            storageKey: 'mirai_registration_{{ $tournament->slug }}',
            lastSaved: false,

            loadSavedData() {
                const saved = localStorage.getItem(this.storageKey);
                if (saved) {
                    try {
                        const data = JSON.parse(saved);
                        // Restore Livewire properties
                        if (data.team_name) @this.set('team_name', data.team_name);
                        if (data.captain_name) @this.set('captain_name', data.captain_name);
                        if (data.whatsapp) @this.set('whatsapp', data.whatsapp);
                        if (data.answers) {
                            Object.keys(data.answers).forEach(key => {
                                @this.set('answers.' + key, data.answers[key]);
                            });
                        }
                        this.lastSaved = true;
                    } catch (e) {
                        console.error('Failed to load saved data', e);
                    }
                }

                // Watch for changes and autosave
                this.$watch('$wire.team_name', () => this.saveData());
                this.$watch('$wire.captain_name', () => this.saveData());
                this.$watch('$wire.whatsapp', () => this.saveData());
                this.$watch('$wire.answers', () => this.saveData(), {
                    deep: true
                });
            },

            saveData() {
                const data = {
                    team_name: @this.get('team_name'),
                    captain_name: @this.get('captain_name'),
                    whatsapp: @this.get('whatsapp'),
                    answers: @this.get('answers'),
                    savedAt: new Date().toISOString()
                };
                localStorage.setItem(this.storageKey, JSON.stringify(data));
                this.lastSaved = true;
            },

            clearSavedData() {
                localStorage.removeItem(this.storageKey);
                this.lastSaved = false;
            }
        }
    }

    // Clear saved data after successful submission
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('registration-submitted', () => {
            localStorage.removeItem('mirai_registration_{{ $tournament->slug }}');
        });
    });
</script>
