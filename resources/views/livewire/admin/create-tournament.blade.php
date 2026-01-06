@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp
<div class="flex flex-col md:flex-row gap-6 items-start relative">
    <!-- LEFT COLUMN: MAIN FORM -->
    <form wire:submit.prevent="save" class="flex-1 space-y-6 w-full">

        <!-- HEADER CONFIG CARD -->
        <div class="bg-[#1a1a1a] p-6 rounded-xl border-t-8 border-cyan-500 shadow-xl space-y-4">
            {{-- Autosave Indicator --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-xs">
                    @if ($saveStatus === 'saving')
                        <x-lucide-loader-2 class="w-4 h-4 animate-spin text-cyan-400" />
                        <span class="text-gray-400">Menyimpan...</span>
                    @elseif($saveStatus === 'saved')
                        <x-lucide-check class="w-4 h-4 text-green-400" />
                        <span class="text-green-400">Tersimpan {{ $lastSaved }}</span>
                    @elseif($saveStatus === 'error')
                        <x-lucide-alert-circle class="w-4 h-4 text-red-400" />
                        <span class="text-red-400">Gagal menyimpan</span>
                    @else
                        <span class="text-gray-600">Autosave aktif</span>
                    @endif
                </div>
                @if ($tournament)
                    <a href="{{ route('register', $tournament->slug) }}" target="_blank"
                        class="text-xs text-cyan-400 hover:underline flex items-center gap-1 cursor-pointer">
                        <x-lucide-external-link class="w-3 h-3" />
                        Preview Form
                    </a>
                @endif
            </div>

            <input type="text" wire:model.live.debounce.1500ms="title" wire:change="autosave" required
                class="w-full bg-transparent border-b border-gray-700 text-3xl font-bold text-white placeholder-gray-600 outline-none pb-2 focus:border-cyan-500 transition"
                placeholder="Judul Turnamen">
            @error('title')
                <span class="text-red-400 text-sm">{{ $message }}</span>
            @enderror

            <textarea wire:model.live.debounce.1500ms="description" wire:change="autosave" rows="3"
                class="w-full bg-transparent border-b border-gray-700 text-sm text-gray-300 placeholder-gray-600 outline-none resize-none h-20 focus:border-cyan-500 transition"
                placeholder="Deskripsi Formulir / Peraturan..."></textarea>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-4 border-t border-gray-800">
                <!-- GAME (Autocomplete) -->
                <div class="space-y-2" x-data="gameAutocomplete(@js($this->gameTypes), '{{ $game_type }}')" @click.outside="showDropdown = false">
                    <label
                        class="text-[10px] uppercase tracking-wider font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent inline-block pr-1">Game</label>
                    <div class="relative">
                        <input type="text" x-model="search" @focus="showDropdown = true; filterGames()"
                            @input="filterGames()" @keydown.arrow-down.prevent="highlightNext()"
                            @keydown.arrow-up.prevent="highlightPrev()" @keydown.enter.prevent="selectHighlighted()"
                            @keydown.escape="showDropdown = false" placeholder="Cari game..."
                            style="color: #22d3ee !important;"
                            class="w-full bg-gradient-to-br from-[#0c0c0f] to-[#0a0a0a] border border-gray-700/80 rounded-xl px-4 py-3 text-sm text-cyan-400 placeholder-gray-600 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/40 transition shadow-[inset_0_1px_0_rgba(255,255,255,0.03)]">
                        <input type="hidden" wire:model="game_type" x-ref="gameTypeInput">

                        <!-- Dropdown Results (only show when search has 2+ characters) -->
                        <div x-show="showDropdown && search.length >= 2 && filteredGames.length > 0"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute z-50 mt-1 w-full bg-[#0a0a0a] border border-cyan-500/30 rounded-xl shadow-2xl shadow-black/50 max-h-60 overflow-auto">
                            <template x-for="(game, index) in filteredGames" :key="game.slug">
                                <button type="button" @click="selectGame(game)" @mouseenter="highlightedIndex = index"
                                    :class="{
                                        'bg-cyan-600/20': highlightedIndex === index,
                                        'hover:bg-gray-800/50': highlightedIndex !== index
                                    }"
                                    class="w-full px-4 py-3 text-left cursor-pointer transition border-b border-gray-800/50 last:border-0">
                                    <div class="text-sm text-gray-200" x-text="game.name"></div>
                                    <div x-show="game.abbreviation" class="text-xs text-cyan-400 mt-0.5"
                                        x-text="game.abbreviation"></div>
                                </button>
                            </template>
                        </div>

                        <!-- No Results -->
                        <div x-show="showDropdown && search.length >= 2 && filteredGames.length === 0"
                            class="absolute z-50 mt-1 w-full bg-[#0a0a0a] border border-cyan-500/30 rounded-xl shadow-2xl shadow-black/50 p-4 text-center text-gray-500 text-sm">
                            Tidak ada game yang cocok
                        </div>
                    </div>
                </div>

                <!-- WAKTU -->
                <div class="space-y-2">
                    <label
                        class="text-[10px] uppercase tracking-wider font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent inline-block pr-1">Waktu</label>
                    <div class="relative group">
                        <div
                            class="absolute -inset-0.5 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-xl opacity-0 group-hover:opacity-20 blur transition duration-300">
                        </div>
                        <input type="datetime-local" wire:model="event_date" wire:change="autosave" required
                            style="color: #06b6d4 !important; color-scheme: dark;"
                            class="relative w-full bg-gray-900/80 backdrop-blur-sm border-2 border-cyan-500/30 rounded-xl px-4 py-3.5 text-sm font-medium focus:border-cyan-400 focus:ring-4 focus:ring-cyan-500/20 focus:outline-none transition-all duration-200 shadow-lg shadow-cyan-500/10 hover:border-cyan-400 hover:shadow-cyan-500/20 [&::-webkit-calendar-picker-indicator]:brightness-0 [&::-webkit-calendar-picker-indicator]:invert [&::-webkit-calendar-picker-indicator]:opacity-70 [&::-webkit-calendar-picker-indicator]:hover:opacity-100 [&::-webkit-calendar-picker-indicator]:cursor-pointer [&::-webkit-calendar-picker-indicator]:scale-110">
                    </div>
                </div>

                <!-- BIAYA -->
                <div class="space-y-2">
                    <label
                        class="text-[10px] uppercase tracking-wider font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent inline-block pr-1">Biaya</label>
                    <div class="relative group">
                        <div
                            class="absolute -inset-0.5 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-xl opacity-0 group-hover:opacity-20 blur transition duration-300">
                        </div>
                        <input type="number" wire:model="fee" wire:change="autosave" min="0" required
                            placeholder="0" style="color: #06b6d4 !important;"
                            class="relative w-full bg-gray-900/80 backdrop-blur-sm border-2 border-cyan-500/30 rounded-xl px-4 py-3.5 text-sm font-medium placeholder:text-gray-500 focus:border-cyan-400 focus:ring-4 focus:ring-cyan-500/20 focus:outline-none transition-all duration-200 shadow-lg shadow-cyan-500/10 hover:border-cyan-400 hover:shadow-cyan-500/20">
                    </div>
                </div>

                <!-- SLOT -->
                <div class="space-y-2">
                    <label
                        class="text-[10px] uppercase tracking-wider font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent inline-block pr-1">Slot</label>
                    <div class="relative group">
                        <div
                            class="absolute -inset-0.5 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-xl opacity-0 group-hover:opacity-20 blur transition duration-300">
                        </div>
                        <input type="number" wire:model="max_slots" wire:change="autosave" min="2"
                            max="256" required placeholder="32" style="color: #06b6d4 !important;"
                            class="relative w-full bg-gray-900/80 backdrop-blur-sm border-2 border-cyan-500/30 rounded-xl px-4 py-3.5 text-sm font-medium placeholder:text-gray-500 focus:border-cyan-400 focus:ring-4 focus:ring-cyan-500/20 focus:outline-none transition-all duration-200 shadow-lg shadow-cyan-500/10 hover:border-cyan-400 hover:shadow-cyan-500/20">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <!-- STATUS -->
                <div class="space-y-2">
                    <label
                        class="text-[10px] uppercase tracking-wider font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent inline-block pr-1">Status</label>
                    <div class="relative group">
                        <div
                            class="absolute -inset-0.5 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-xl opacity-0 group-hover:opacity-20 blur transition duration-300">
                        </div>
                        <select wire:model="status" style="color: #06b6d4 !important; color-scheme: dark;"
                            class="relative w-full bg-gray-900/80 backdrop-blur-sm border-2 border-cyan-500/30 rounded-xl px-4 py-3.5 text-sm font-medium cursor-pointer focus:border-cyan-400 focus:ring-4 focus:ring-cyan-500/20 focus:outline-none transition-all duration-200 shadow-lg shadow-cyan-500/10 hover:border-cyan-400 hover:shadow-cyan-500/20 [&>option]:bg-gray-900 [&>option]:text-cyan-400 [&>option]:py-3">
                            <option value="draft" style="color: #22d3ee !important;">Draft</option>
                            <option value="open" style="color: #22d3ee !important;">Open</option>
                            <option value="closed" style="color: #22d3ee !important;">Closed</option>
                        </select>
                    </div>
                </div>
                <!-- BANNER -->
                <div class="space-y-2">
                    <label
                        class="text-[10px] uppercase tracking-wider font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent inline-block pr-1">Banner</label>
                    <label
                        class="flex items-center gap-2 px-3 py-2 bg-black border border-gray-700 rounded cursor-pointer hover:border-cyan-500 transition w-fit">
                        <svg class="w-4 h-4 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span class="text-xs text-gray-300">Choose File</span>
                        <input type="file" wire:model.live="banner" accept="image/*" class="hidden">
                    </label>
                    <div wire:loading wire:target="banner" class="mt-2 flex items-center gap-2 text-cyan-400 text-xs">
                        <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                        <span>Uploading...</span>
                    </div>
                    @if ($banner)
                        <div class="mt-2" wire:loading.remove wire:target="banner">
                            @php $bannerSrc = $banner->temporaryUrl(); @endphp
                            <div class="relative inline-block group" x-data="bannerPreview('{{ $bannerSrc }}')">
                                <img src="{{ $bannerSrc }}"
                                    class="h-20 rounded border border-gray-700 object-cover cursor-pointer"
                                    alt="Banner Preview" @click="showFullscreen = true">
                                <div
                                    class="absolute top-1 right-1 flex gap-1 opacity-0 group-hover:opacity-100 transition">
                                    <button type="button" @click.stop="showFullscreen = true"
                                        class="p-1 bg-black/70 rounded text-white hover:text-cyan-400 cursor-pointer"
                                        title="Fullscreen">
                                        <x-lucide-maximize class="w-4 h-4" />
                                    </button>
                                    <button type="button" @click.stop="openTab()"
                                        class="p-1 bg-black/70 rounded text-white hover:text-cyan-400 cursor-pointer"
                                        title="Buka tab baru">
                                        <x-lucide-external-link class="w-4 h-4" />
                                    </button>
                                </div>
                                <template x-teleport="body">
                                    <div x-show="showFullscreen" x-cloak
                                        class="fixed inset-0 z-[99999] bg-black/90 flex items-center justify-center p-4 cursor-pointer"
                                        @click="showFullscreen = false"
                                        @keydown.escape.window="showFullscreen = false" x-transition>
                                        <div
                                            class="absolute top-4 left-1/2 -translate-x-1/2 flex gap-2 bg-black/80 backdrop-blur-sm px-3 py-2 rounded-full border border-gray-700">
                                            <button type="button" @click.stop="openTab()"
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
                                        <img :src="src"
                                            class="max-w-full max-h-[90vh] object-contain cursor-default" @click.stop>
                                    </div>
                                </template>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- DYNAMIC FIELDS -->
        <div class="space-y-4 pb-20">
            @forelse($fields as $index => $field)
                <div class="bg-[#1a1a1a] p-5 rounded-xl border-l-4 border-cyan-500 shadow-lg"
                    wire:key="field-{{ $field['id'] ?? $index }}">

                    @if ($field['type'] === 'section')
                        {{-- SECTION TYPE --}}
                        <div
                            class="bg-gradient-to-r from-cyan-900/30 to-blue-900/30 -m-5 p-5 rounded-xl border-2 border-dashed border-cyan-500/50">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-2 text-cyan-400">
                                    <x-lucide-layout-grid class="w-5 h-5" />
                                    <span class="text-xs font-bold uppercase tracking-widest">Bagian / Section</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" wire:click="moveFieldUp({{ $index }})"
                                        class="p-1 text-gray-500 hover:text-cyan-400 cursor-pointer {{ $index === 0 ? 'opacity-30 !cursor-not-allowed' : '' }}"
                                        {{ $index === 0 ? 'disabled' : '' }}>
                                        <x-lucide-chevron-up class="w-4 h-4" />
                                    </button>
                                    <button type="button" wire:click="moveFieldDown({{ $index }})"
                                        class="p-1 text-gray-500 hover:text-cyan-400 cursor-pointer {{ $index === count($fields) - 1 ? 'opacity-30 !cursor-not-allowed' : '' }}"
                                        {{ $index === count($fields) - 1 ? 'disabled' : '' }}>
                                        <x-lucide-chevron-down class="w-4 h-4" />
                                    </button>
                                    <button type="button" wire:click="removeField({{ $index }})"
                                        class="p-1 text-gray-500 hover:text-red-500 cursor-pointer">
                                        <x-lucide-trash-2 class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                            <input type="text" wire:model="fields.{{ $index }}.label"
                                class="w-full bg-transparent border-b-2 border-cyan-500/50 text-xl font-bold text-white placeholder-gray-500 outline-none pb-2 focus:border-cyan-400 transition"
                                placeholder="Judul Bagian">
                            <textarea wire:model="fields.{{ $index }}.description" rows="2"
                                class="w-full bg-transparent border-b border-gray-700 text-sm text-gray-400 placeholder-gray-600 outline-none resize-none mt-3 focus:border-cyan-500 transition"
                                placeholder="Deskripsi bagian (opsional)"></textarea>
                            <p class="text-[10px] text-cyan-500/70 mt-3 flex items-center gap-1">
                                <x-lucide-info class="w-3 h-3" />
                                Pertanyaan setelah ini akan muncul di halaman baru
                            </p>
                        </div>
                    @elseif(in_array($field['type'], ['note', 'image-view', 'link']))
                        {{-- STATIC BLOCKS --}}
                        <div class="space-y-3">
                            <div
                                class="flex justify-between items-center text-cyan-500 text-xs font-bold uppercase tracking-widest mb-2">
                                <span class="flex items-center gap-2">
                                    @if ($field['type'] === 'note')
                                        <x-lucide-text class="w-4 h-4" /> Teks / Judul
                                    @elseif($field['type'] === 'image-view')
                                        <x-lucide-image class="w-4 h-4" /> Gambar Banner
                                    @else
                                        <x-lucide-link class="w-4 h-4" /> Link Eksternal
                                    @endif
                                </span>
                                <button type="button" wire:click="removeField({{ $index }})"
                                    class="text-gray-600 hover:text-red-500 cursor-pointer">
                                    <x-lucide-trash-2 class="w-4 h-4" />
                                </button>
                            </div>
                            <input type="text" wire:model="fields.{{ $index }}.label"
                                class="w-full bg-transparent border-b border-gray-700 p-2 text-lg font-bold text-white outline-none focus:border-cyan-500"
                                placeholder="{{ $field['type'] === 'image-view' ? 'Judul Gambar (Opsional)' : 'Judul / Label' }}">

                            @if ($field['type'] === 'note')
                                <textarea wire:model="fields.{{ $index }}.content" rows="3"
                                    class="w-full bg-black/50 border border-gray-700 rounded p-2 text-sm text-white outline-none h-20 focus:border-cyan-500"
                                    placeholder="Isi teks..."></textarea>
                            @elseif($field['type'] === 'image-view')
                                <div class="space-y-2">
                                    <input type="file" wire:model="imageUploads.{{ $index }}"
                                        accept="image/*"
                                        class="w-full bg-black border border-gray-700 rounded p-2 text-sm text-white outline-none focus:border-cyan-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:bg-cyan-900 file:text-cyan-400 file:text-xs file:cursor-pointer cursor-pointer">
                                    <div wire:loading wire:target="imageUploads.{{ $index }}"
                                        class="flex items-center gap-2 text-cyan-400 text-xs">
                                        <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                                        <span>Uploading...</span>
                                    </div>
                                    @php
                                        $imagePath = $field['description'] ?? ($field['content'] ?? '');
                                        $imageSrc = $imagePath
                                            ? (Str::startsWith($imagePath, ['http://', 'https://'])
                                                ? $imagePath
                                                : Storage::url($imagePath))
                                            : null;
                                    @endphp
                                    @if ($imageSrc)
                                        <div class="relative group inline-block" x-data="{ showFullscreen: false }">
                                            <img src="{{ $imageSrc }}"
                                                class="h-24 rounded border border-gray-700 object-cover mt-2 cursor-pointer"
                                                alt="Preview" @click="showFullscreen = true">
                                            <div
                                                class="absolute top-1 right-1 flex gap-1 opacity-0 group-hover:opacity-100 transition">
                                                <button type="button" @click.stop="showFullscreen = true"
                                                    class="p-1 bg-black/70 rounded text-white hover:text-cyan-400 cursor-pointer"
                                                    title="Fullscreen">
                                                    <x-lucide-maximize class="w-4 h-4" />
                                                </button>
                                                <button type="button"
                                                    @click.stop="window.open('{{ $imageSrc }}','_blank')"
                                                    class="p-1 bg-black/70 rounded text-white hover:text-cyan-400 cursor-pointer"
                                                    title="Buka tab baru">
                                                    <x-lucide-external-link class="w-4 h-4" />
                                                </button>
                                            </div>
                                            <template x-teleport="body">
                                                <div x-show="showFullscreen" x-cloak
                                                    class="fixed inset-0 z-[99999] bg-black/90 flex items-center justify-center p-4 cursor-pointer"
                                                    @click="showFullscreen = false"
                                                    @keydown.escape.window="showFullscreen = false" x-transition>
                                                    <div
                                                        class="absolute top-4 left-1/2 -translate-x-1/2 flex gap-2 bg-black/80 backdrop-blur-sm px-3 py-2 rounded-full border border-gray-700">
                                                        <button type="button"
                                                            @click.stop="window.open('{{ $imageSrc }}','_blank')"
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
                            @else
                                <input type="text" wire:model="fields.{{ $index }}.content"
                                    class="w-full bg-black border border-gray-700 rounded p-2 text-sm text-white outline-none focus:border-cyan-500"
                                    placeholder="URL Link (https://...)">
                            @endif
                        </div>
                    @else
                        {{-- QUESTION TYPES --}}
                        <div class="space-y-4">
                            <div class="flex flex-col md:flex-row gap-4">
                                <div class="flex-1">
                                    <input type="text" wire:model="fields.{{ $index }}.label"
                                        class="w-full bg-[#222] border-b border-gray-700 p-3 text-base text-white outline-none focus:border-cyan-500 rounded-t"
                                        placeholder="Pertanyaan">
                                </div>
                                <div class="w-full md:w-40">
                                    <select wire:model.live="fields.{{ $index }}.type"
                                        wire:change="updateFieldType({{ $index }}, $event.target.value)"
                                        class="w-full bg-[#222] border border-gray-700 rounded p-2 text-sm text-gray-300 focus:border-cyan-500 outline-none cursor-pointer">
                                        <option value="text">Jawaban Singkat</option>
                                        <option value="textarea">Paragraf</option>
                                        <option value="radio">Pilihan Ganda</option>
                                        <option value="checkbox">Kotak Centang</option>
                                        <option value="select">Dropdown</option>
                                        <option value="file">Upload File</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Media Embedder --}}
                            <div class="flex flex-col gap-3 bg-[#111] p-3 rounded border border-gray-800">
                                <div class="flex items-center gap-3">
                                    <span class="text-[10px] text-gray-500 font-bold uppercase mr-2">Sisipkan:</span>
                                    <button type="button" wire:click="toggleImageInput({{ $index }})"
                                        class="p-1 rounded transition cursor-pointer {{ !empty($field['imageUrl']) || ($field['showImageInput'] ?? false) ? 'text-cyan-400 bg-cyan-900/30' : 'text-gray-500 hover:text-white' }}">
                                        <x-lucide-image class="w-4 h-4" />
                                    </button>
                                    <button type="button" wire:click="toggleLinkInput({{ $index }})"
                                        class="p-1 rounded transition cursor-pointer {{ $field['showLinkInput'] ?? false ? 'text-cyan-400 bg-cyan-900/30' : 'text-gray-500 hover:text-white' }}">
                                        <x-lucide-link class="w-4 h-4" />
                                    </button>
                                </div>

                                @if (($field['showImageInput'] ?? false) || !empty($field['imageUrl']))
                                    @php
                                        $embedImage = $field['imageUrl'] ?? '';
                                        $embedSrc = $embedImage
                                            ? (Str::startsWith($embedImage, ['http://', 'https://'])
                                                ? $embedImage
                                                : Storage::url($embedImage))
                                            : null;
                                    @endphp
                                    <div class="space-y-2 bg-black/30 p-2 rounded border border-gray-800">
                                        <div class="flex gap-2 items-center">
                                            <input type="file" wire:model="imageUploads.{{ $index }}"
                                                accept="image/*"
                                                class="flex-1 bg-black text-xs text-white border border-gray-700 rounded px-2 py-1 outline-none focus:border-cyan-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:bg-cyan-900 file:text-cyan-400 file:text-xs file:cursor-pointer cursor-pointer">
                                            <button type="button" wire:click="clearImage({{ $index }})"
                                                class="text-gray-600 hover:text-red-500 cursor-pointer p-1"
                                                title="Hapus gambar">
                                                <x-lucide-x class="w-4 h-4" />
                                            </button>
                                        </div>
                                        <div wire:loading wire:target="imageUploads.{{ $index }}"
                                            class="flex items-center gap-2 text-cyan-400 text-xs">
                                            <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                                            <span>Uploading...</span>
                                        </div>
                                        @if ($embedSrc)
                                            <div class="relative group inline-block" x-data="{ showFullscreen: false }">
                                                <img src="{{ $embedSrc }}"
                                                    class="h-20 rounded border border-gray-600 object-cover cursor-pointer"
                                                    alt="Preview" @click="showFullscreen = true">
                                                <div
                                                    class="absolute top-1 right-1 flex gap-1 opacity-0 group-hover:opacity-100 transition">
                                                    <button type="button" @click.stop="showFullscreen = true"
                                                        class="p-1 bg-black/70 rounded text-white hover:text-cyan-400 cursor-pointer"
                                                        title="Fullscreen">
                                                        <x-lucide-maximize class="w-4 h-4" />
                                                    </button>
                                                    <button type="button"
                                                        @click.stop="window.open('{{ $embedSrc }}','_blank')"
                                                        class="p-1 bg-black/70 rounded text-white hover:text-cyan-400 cursor-pointer"
                                                        title="Buka tab baru">
                                                        <x-lucide-external-link class="w-4 h-4" />
                                                    </button>
                                                </div>
                                                <template x-teleport="body">
                                                    <div x-show="showFullscreen" x-cloak
                                                        class="fixed inset-0 z-[99999] bg-black/90 flex items-center justify-center p-4 cursor-pointer"
                                                        @click="showFullscreen = false"
                                                        @keydown.escape.window="showFullscreen = false" x-transition>
                                                        <div
                                                            class="absolute top-4 left-1/2 -translate-x-1/2 flex gap-2 bg-black/80 backdrop-blur-sm px-3 py-2 rounded-full border border-gray-700">
                                                            <button type="button"
                                                                @click.stop="window.open('{{ $embedSrc }}','_blank')"
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
                                    </div>
                                @endif

                                @if ($field['showLinkInput'] ?? false)
                                    <div
                                        class="flex gap-2 bg-black/30 p-2 rounded border border-gray-800 items-center">
                                        <div class="flex-1 flex flex-col gap-2">
                                            <input type="text" wire:model="fields.{{ $index }}.linkUrl"
                                                class="bg-black text-xs text-white border border-gray-700 rounded px-2 py-1 outline-none focus:border-cyan-500"
                                                placeholder="URL Link (https://...)">
                                            <input type="text" wire:model="fields.{{ $index }}.linkText"
                                                class="bg-black text-xs text-white border border-gray-700 rounded px-2 py-1 outline-none focus:border-cyan-500"
                                                placeholder="Teks Link (Optional)">
                                        </div>
                                        <button type="button" wire:click="clearLink({{ $index }})"
                                            class="text-gray-600 hover:text-red-500 cursor-pointer p-1">
                                            <x-lucide-x class="w-4 h-4" />
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <input type="text" wire:model="fields.{{ $index }}.description"
                                class="w-full bg-transparent border-b border-dashed border-gray-800 text-xs text-gray-500 focus:text-white p-1 outline-none"
                                placeholder="Deskripsi (Opsional)">

                            {{-- File Settings --}}
                            @if ($field['type'] === 'file')
                                <div
                                    class="ml-2 mt-4 space-y-4 border-l-2 border-cyan-500 pl-4 bg-cyan-900/10 p-4 rounded-r-lg">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-300">Batasi jenis file</span>
                                        <button type="button" wire:click="toggleRestrictTypes({{ $index }})"
                                            class="relative inline-flex h-5 w-10 items-center rounded-full transition cursor-pointer {{ $field['fileSettings']['restrictTypes'] ?? false ? 'bg-cyan-600' : 'bg-gray-600' }}">
                                            <span
                                                class="inline-block h-3 w-3 transform rounded-full bg-white transition {{ $field['fileSettings']['restrictTypes'] ?? false ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                        </button>
                                    </div>
                                    @if ($field['fileSettings']['restrictTypes'] ?? false)
                                        <div class="grid grid-cols-2 gap-3">
                                            @foreach (['image' => 'Gambar', 'pdf' => 'PDF', 'document' => 'Dokumen', 'video' => 'Video'] as $typeKey => $typeLabel)
                                                <label
                                                    class="flex items-center gap-2 cursor-pointer text-sm text-gray-400 hover:text-white">
                                                    <input type="checkbox"
                                                        wire:click="toggleFileType({{ $index }}, '{{ $typeKey }}')"
                                                        {{ in_array($typeKey, $field['fileSettings']['allowedTypes'] ?? []) ? 'checked' : '' }}
                                                        class="accent-cyan-500 w-4 h-4 rounded">
                                                    <span>{{ $typeLabel }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-300">Max size</span>
                                        <select wire:model="fields.{{ $index }}.fileSettings.maxSize"
                                            class="bg-black border border-gray-700 rounded p-1 text-sm text-white cursor-pointer">
                                            <option value="524288">500 KB</option>
                                            <option value="1048576">1 MB</option>
                                            <option value="2097152">2 MB</option>
                                            <option value="5242880">5 MB</option>
                                        </select>
                                    </div>
                                </div>
                            @endif

                            {{-- Options for radio/checkbox/select --}}
                            @if (in_array($field['type'], ['radio', 'checkbox', 'select']))
                                <div class="ml-2 space-y-2 pl-2 border-l-2 border-gray-800">
                                    @foreach ($field['options'] ?? [] as $optIndex => $option)
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-600 text-xs">{{ $optIndex + 1 }}.</span>
                                            <input type="text"
                                                wire:model="fields.{{ $index }}.options.{{ $optIndex }}"
                                                class="bg-transparent border-b border-gray-800 hover:border-gray-600 focus:border-cyan-500 text-sm text-gray-300 w-full p-1 outline-none"
                                                placeholder="Opsi {{ $optIndex + 1 }}">
                                            <button type="button"
                                                wire:click="removeOption({{ $index }}, {{ $optIndex }})"
                                                class="text-gray-600 hover:text-red-500 cursor-pointer">
                                                <x-lucide-x class="w-4 h-4" />
                                            </button>
                                        </div>
                                    @endforeach
                                    <button type="button" wire:click="addOption({{ $index }})"
                                        class="text-cyan-500 text-xs hover:underline flex items-center gap-1 mt-2 p-1 cursor-pointer">
                                        <x-lucide-plus class="w-3 h-3" /> Tambah Opsi
                                    </button>
                                </div>
                            @endif

                            {{-- Footer Actions --}}
                            <div class="flex justify-between items-center gap-4 pt-4 border-t border-gray-800 mt-4">
                                <div class="flex items-center gap-1">
                                    <button type="button" wire:click="moveFieldUp({{ $index }})"
                                        class="p-1.5 rounded transition cursor-pointer {{ $index === 0 ? 'text-gray-700 !cursor-not-allowed' : 'text-gray-500 hover:text-cyan-400 hover:bg-cyan-900/20' }}"
                                        {{ $index === 0 ? 'disabled' : '' }}>
                                        <x-lucide-chevron-up class="w-4 h-4" />
                                    </button>
                                    <button type="button" wire:click="moveFieldDown({{ $index }})"
                                        class="p-1.5 rounded transition cursor-pointer {{ $index === count($fields) - 1 ? 'text-gray-700 !cursor-not-allowed' : 'text-gray-500 hover:text-cyan-400 hover:bg-cyan-900/20' }}"
                                        {{ $index === count($fields) - 1 ? 'disabled' : '' }}>
                                        <x-lucide-chevron-down class="w-4 h-4" />
                                    </button>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-2 border-r border-gray-700 pr-4">
                                        <span class="text-xs text-gray-500 cursor-pointer"
                                            wire:click="toggleRequired({{ $index }})">Wajib</span>
                                        <button type="button" wire:click="toggleRequired({{ $index }})"
                                            class="w-8 h-4 rounded-full cursor-pointer relative transition {{ $field['required'] ?? false ? 'bg-cyan-600' : 'bg-gray-700' }}">
                                            <div
                                                class="w-2 h-2 bg-white rounded-full absolute top-1 transition-all {{ $field['required'] ?? false ? 'left-5' : 'left-1' }}">
                                            </div>
                                        </button>
                                    </div>
                                    <button type="button" wire:click="duplicateField({{ $index }})"
                                        class="text-gray-500 hover:text-white p-2 cursor-pointer" title="Duplikasi">
                                        <x-lucide-copy class="w-4 h-4" />
                                    </button>
                                    <button type="button" wire:click="removeField({{ $index }})"
                                        class="text-gray-500 hover:text-red-500 p-2 cursor-pointer" title="Hapus">
                                        <x-lucide-trash-2 class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-[#1a1a1a] p-12 rounded-xl border border-dashed border-gray-700 text-center">
                    <p class="text-gray-500">Belum ada field custom</p>
                </div>
            @endforelse
        </div>

        {{-- Submit Buttons --}}
        <div class="flex items-center gap-4 pt-4 border-t border-gray-800">
            <button type="button" onclick="window.location='{{ route('admin.tournaments.index') }}'"
                class="px-6 py-3 border border-gray-700 rounded-lg text-gray-300 hover:text-white hover:border-gray-500 transition cursor-pointer">Batal</button>
            <button type="submit"
                class="flex-1 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold py-3 rounded-lg shadow-lg transition cursor-pointer">
                Buat Turnamen
            </button>
        </div>
    </form>

    <!-- FLOATING SIDEBAR (Desktop) -->
    <div
        class="sticky top-24 bg-[#1a1a1a] border border-gray-700 rounded-lg shadow-2xl p-2 hidden md:flex flex-col gap-3">
        <button type="button" wire:click="addField('text')"
            class="p-2 text-gray-400 hover:text-cyan-400 hover:bg-cyan-950/50 rounded transition group relative cursor-pointer">
            <x-lucide-plus class="w-6 h-6" />
            <span
                class="absolute right-full mr-2 top-1.5 bg-black text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 whitespace-nowrap pointer-events-none">Tambah
                Pertanyaan</span>
        </button>
        <button type="button" wire:click="addField('note')"
            class="p-2 text-gray-400 hover:text-cyan-400 hover:bg-cyan-950/50 rounded transition group relative cursor-pointer">
            <x-lucide-text class="w-5 h-5" />
            <span
                class="absolute right-full mr-2 top-1.5 bg-black text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 whitespace-nowrap pointer-events-none">Tambah
                Teks</span>
        </button>
        <button type="button" wire:click="addField('image-view')"
            class="p-2 text-gray-400 hover:text-cyan-400 hover:bg-cyan-950/50 rounded transition group relative cursor-pointer">
            <x-lucide-image class="w-5 h-5" />
            <span
                class="absolute right-full mr-2 top-1.5 bg-black text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 whitespace-nowrap pointer-events-none">Tambah
                Gambar</span>
        </button>
        <button type="button" wire:click="addField('link')"
            class="p-2 text-gray-400 hover:text-cyan-400 hover:bg-cyan-950/50 rounded transition group relative cursor-pointer">
            <x-lucide-link class="w-5 h-5" />
            <span
                class="absolute right-full mr-2 top-1.5 bg-black text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 whitespace-nowrap pointer-events-none">Tambah
                Link</span>
        </button>
        <div class="border-t border-gray-700 my-1"></div>
        <button type="button" wire:click="addField('section')"
            class="p-2 text-gray-400 hover:text-cyan-400 hover:bg-cyan-950/50 rounded transition group relative cursor-pointer">
            <x-lucide-layout-grid class="w-5 h-5" />
            <span
                class="absolute right-full mr-2 top-1.5 bg-black text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 whitespace-nowrap pointer-events-none">Tambah
                Bagian</span>
        </button>
    </div>

    <!-- MOBILE BOTTOM BAR -->
    <div
        class="fixed bottom-0 left-0 w-full bg-[#1a1a1a] border-t border-gray-800 p-3 flex justify-around md:hidden z-50">
        <button type="button" wire:click="addField('text')"
            class="text-gray-400 hover:text-cyan-400 flex flex-col items-center gap-1 cursor-pointer">
            <x-lucide-plus class="w-6 h-6" />
            <span class="text-[10px]">Pertanyaan</span>
        </button>
        <button type="button" wire:click="addField('note')"
            class="text-gray-400 hover:text-cyan-400 flex flex-col items-center gap-1 cursor-pointer">
            <x-lucide-text class="w-6 h-6" />
            <span class="text-[10px]">Teks</span>
        </button>
        <button type="button" wire:click="addField('image-view')"
            class="text-gray-400 hover:text-cyan-400 flex flex-col items-center gap-1 cursor-pointer">
            <x-lucide-image class="w-6 h-6" />
            <span class="text-[10px]">Gambar</span>
        </button>
        <button type="button" wire:click="addField('link')"
            class="text-gray-400 hover:text-cyan-400 flex flex-col items-center gap-1 cursor-pointer">
            <x-lucide-link class="w-6 h-6" />
            <span class="text-[10px]">Link</span>
        </button>
        <button type="button" wire:click="addField('section')"
            class="text-gray-400 hover:text-cyan-400 flex flex-col items-center gap-1 cursor-pointer">
            <x-lucide-layout-grid class="w-6 h-6" />
            <span class="text-[10px]">Bagian</span>
        </button>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        // Game Autocomplete Component
        Alpine.data('gameAutocomplete', (gameTypes, initialSlug) => ({
            games: Object.entries(gameTypes).map(([slug, data]) => ({
                slug,
                name: data.name,
                abbreviation: data.abbreviation
            })),
            search: '',
            selectedSlug: initialSlug,
            filteredGames: [],
            showDropdown: false,
            highlightedIndex: 0,

            init() {
                // Set initial search value from selected slug
                const found = this.games.find(g => g.slug === this.selectedSlug);
                if (found) {
                    this.search = found.name;
                }
                this.filteredGames = this.games;
            },

            filterGames() {
                const query = this.search.toLowerCase().trim();
                if (!query) {
                    this.filteredGames = this.games;
                } else {
                    this.filteredGames = this.games.filter(g =>
                        g.name.toLowerCase().includes(query) ||
                        (g.abbreviation && g.abbreviation.toLowerCase().includes(query))
                    );
                }
                this.highlightedIndex = 0;
            },

            selectGame(game) {
                this.search = game.name;
                this.selectedSlug = game.slug;
                this.$refs.gameTypeInput.value = game.slug;
                this.$refs.gameTypeInput.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
                this.showDropdown = false;
            },

            highlightNext() {
                if (this.highlightedIndex < this.filteredGames.length - 1) {
                    this.highlightedIndex++;
                }
            },

            highlightPrev() {
                if (this.highlightedIndex > 0) {
                    this.highlightedIndex--;
                }
            },

            selectHighlighted() {
                if (this.filteredGames[this.highlightedIndex]) {
                    this.selectGame(this.filteredGames[this.highlightedIndex]);
                }
            }
        }));

        window.bannerPreview = (src) => ({
            src,
            showFullscreen: false,
            openTab() {
                const w = window.open('', '_blank');
                if (!w) return;
                w.document.write(
                    `<html><head><title>Preview</title><style>body{margin:0;background:#000;display:flex;justify-content:center;align-items:center;min-height:100vh;}img{max-width:100%;max-height:100vh;object-fit:contain;}</style></head><body><img src='${this.src}'/></body></html>`
                );
                w.document.close();
            },
        });

        // tint date/time icons cyan for admin forms
        const pickers = document.querySelectorAll(
            "input[type='date'], input[type='time'], input[type='datetime-local']");
        pickers.forEach(el => {
            el.style.setProperty('--webkit-calendar-picker-indicator-filter',
                'invert(0.5) sepia(1) saturate(5) hue-rotate(150deg)');
        });
    });
</script>
