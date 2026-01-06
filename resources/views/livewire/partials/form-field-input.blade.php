{{-- Form Field Input Partial --}}
@switch($field['type'])
    @case('text')
    @case('email')
    @case('number')
        <input type="{{ $field['type'] }}" 
               wire:model="answers.{{ $field['id'] }}"
               placeholder="{{ $field['placeholder'] ?? '' }}"
               class="w-full bg-black/50 border border-gray-700 rounded p-3 text-white text-sm outline-none focus:border-cyan-500">
        @error("answers.{$field['id']}") <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
        @break

    @case('textarea')
        <textarea wire:model="answers.{{ $field['id'] }}"
                  placeholder="{{ $field['placeholder'] ?? '' }}"
                  rows="4"
                  class="w-full bg-black/50 border border-gray-700 rounded p-3 text-white text-sm outline-none focus:border-cyan-500 resize-none h-24"></textarea>
        @error("answers.{$field['id']}") <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
        @break

    @case('select')
        <div class="relative">
            <select wire:model="answers.{{ $field['id'] }}"
                    class="w-full bg-black/50 border border-gray-700 rounded p-3 pr-10 text-white text-sm appearance-none outline-none focus:border-cyan-500">
                <option value="">Pilih...</option>
                @foreach($field['options'] ?? [] as $option)
                    @if($option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endif
                @endforeach
            </select>
            <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        @error("answers.{$field['id']}") <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
        @break

    @case('radio')
        <div class="space-y-2">
            @foreach($field['options'] ?? [] as $option)
                @if($option)
                    <label class="flex items-center gap-3 text-sm text-gray-300 cursor-pointer hover:text-white">
                        <input type="radio" 
                               wire:model="answers.{{ $field['id'] }}" 
                               value="{{ $option }}"
                               class="accent-cyan-500 w-4 h-4">
                        {{ $option }}
                    </label>
                @endif
            @endforeach
        </div>
        @error("answers.{$field['id']}") <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
        @break

    @case('checkbox')
        <div class="space-y-2">
            @foreach($field['options'] ?? [] as $option)
                @if($option)
                    <label class="flex items-center gap-3 text-sm text-gray-300 cursor-pointer hover:text-white">
                        <input type="checkbox" 
                               wire:model="answers.{{ $field['id'] }}" 
                               value="{{ $option }}"
                               class="accent-cyan-500 w-4 h-4">
                        {{ $option }}
                    </label>
                @endif
            @endforeach
        </div>
        @error("answers.{$field['id']}") <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
        @break

    @case('file')
        @php
            $fileSettings = $field['fileSettings'] ?? [];
            $restrictTypes = $fileSettings['restrictTypes'] ?? false;
            $allowedTypes = $fileSettings['allowedTypes'] ?? [];
            $maxSize = $fileSettings['maxSize'] ?? 1048576;
            
            $acceptAttr = '*';
            if ($restrictTypes && count($allowedTypes) > 0) {
                $accepts = [];
                foreach ($allowedTypes as $type) {
                    match($type) {
                        'image' => $accepts[] = 'image/*',
                        'pdf' => $accepts[] = 'application/pdf',
                        'video' => $accepts[] = 'video/*',
                        'audio' => $accepts[] = 'audio/*',
                        'document' => $accepts[] = '.doc,.docx',
                        'spreadsheet' => $accepts[] = '.xls,.xlsx',
                        default => null,
                    };
                }
                $acceptAttr = implode(',', $accepts) ?: '*';
            }
            
            $maxSizeText = $maxSize >= 1048576 
                ? round($maxSize / 1048576, 1) . ' MB' 
                : round($maxSize / 1024) . ' KB';
            
            $fieldId = $field['id'];
        @endphp
        <div x-data="fileUploader_{{ Str::slug($fieldId, '_') }}()" @keydown.escape.window="closeLightbox()">
            {{-- File Input Area --}}
            <label class="flex items-center gap-3 px-4 py-3 bg-black/50 border border-gray-700 rounded cursor-pointer hover:border-cyan-500 transition">
                <svg class="w-5 h-5 text-cyan-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                <span class="text-sm text-gray-400 flex-1" x-text="fileName || 'Pilih file...'"></span>
                <input type="file" 
                       x-ref="fileInput"
                       wire:model="fileUploads.{{ $fieldId }}"
                       accept="{{ $acceptAttr }}"
                       @change="handleFile($event)"
                       class="hidden">
            </label>
            
            {{-- Preview Card (shows after file selected) --}}
            <div x-show="fileName" x-cloak class="mt-3 bg-black/30 border border-gray-700 rounded-lg overflow-hidden">
                <div class="flex items-start gap-3 p-3">
                    {{-- Thumbnail --}}
                    <div class="flex-shrink-0">
                        <template x-if="preview">
                            <img :src="preview" 
                                 @click="openLightbox()" 
                                 class="w-16 h-16 rounded object-cover cursor-pointer hover:opacity-80 transition border border-gray-600" 
                                 alt="Preview">
                        </template>
                        <template x-if="!preview && fileType">
                            <div class="w-16 h-16 rounded bg-gray-800 flex items-center justify-center border border-gray-600">
                                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </template>
                    </div>
                    
                    {{-- File Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-white truncate" x-text="fileName"></p>
                        <p class="text-xs text-gray-500 mt-1" x-text="fileSize"></p>
                    </div>
                    
                    {{-- Actions --}}
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button type="button" x-show="preview" @click="openLightbox()" class="p-2 text-gray-400 hover:text-cyan-400 transition cursor-pointer" title="Lihat">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                            </svg>
                        </button>
                        <button type="button" @click="clearFile()" class="p-2 text-gray-400 hover:text-red-400 transition cursor-pointer" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            {{-- Lightbox/Fullscreen Modal --}}
            <div x-show="showLightbox" 
                 x-cloak
                 style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 99999;"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click.self="closeLightbox()"
                 class="bg-black flex items-center justify-center cursor-pointer">
                {{-- Close & Actions Buttons --}}
                <div class="absolute top-4 left-1/2 -translate-x-1/2 flex gap-2 bg-black/80 backdrop-blur-sm px-3 py-2 rounded-full border border-gray-700">
                    <button type="button" @click="openInNewTab()" class="p-2 text-white hover:text-cyan-400 transition cursor-pointer" title="Buka di tab baru">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </button>
                    <div class="w-px bg-gray-600"></div>
                    <button type="button" @click="closeLightbox()" class="p-2 text-white hover:text-red-400 transition cursor-pointer" title="Tutup (ESC)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                {{-- Image --}}
                <img :src="preview" class="max-w-[95vw] max-h-[95vh] object-contain cursor-default" alt="Preview" @click.stop>
            </div>
            
            <p class="text-xs text-gray-500 mt-2">Maksimal {{ $maxSizeText }}</p>
        </div>
        <script>
            document.addEventListener('alpine:init', () => {
                if (!Alpine.data.hasOwnProperty('fileUploader_{{ Str::slug($fieldId, '_') }}')) {
                    Alpine.data('fileUploader_{{ Str::slug($fieldId, '_') }}', () => ({
                        preview: null,
                        fileName: null,
                        fileType: null,
                        fileSize: null,
                        showLightbox: false,
                        formatFileSize(bytes) {
                            if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
                            if (bytes >= 1024) return (bytes / 1024).toFixed(0) + ' KB';
                            return bytes + ' bytes';
                        },
                        handleFile(event) {
                            const file = event.target.files[0];
                            if (!file) return;
                            
                            // Client-side file size validation
                            const maxSize = {{ $maxSize }};
                            if (file.size > maxSize) {
                                alert('File terlalu besar! Maksimal {{ $maxSizeText }}');
                                this.clearFile();
                                return;
                            }
                            
                            this.fileName = file.name;
                            this.fileType = file.type;
                            this.fileSize = this.formatFileSize(file.size);
                            if (file.type.startsWith('image/')) {
                                const reader = new FileReader();
                                reader.onload = (e) => this.preview = e.target.result;
                                reader.readAsDataURL(file);
                            } else {
                                this.preview = null;
                            }
                        },
                        clearFile() {
                            this.preview = null;
                            this.fileName = null;
                            this.fileType = null;
                            this.fileSize = null;
                            this.$refs.fileInput.value = '';
                            this.$wire.set('fileUploads.{{ $fieldId }}', null);
                        },
                        openLightbox() {
                            this.showLightbox = true;
                            document.body.style.overflow = 'hidden';
                        },
                        closeLightbox() {
                            this.showLightbox = false;
                            document.body.style.overflow = '';
                        },
                        openInNewTab() {
                            if (this.preview) {
                                const w = window.open('', '_blank');
                                w.document.write('<html><head><title>Preview</title><style>body{margin:0;background:#000;display:flex;justify-content:center;align-items:center;min-height:100vh;}img{max-width:100%;max-height:100vh;object-fit:contain;}</style></head><body><img src="' + this.preview + '"/></body></html>');
                                w.document.close();
                            }
                        }
                    }));
                }
            });
        </script>
        @error("fileUploads.{$field['id']}") <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
        @break

    @case('date')
        <input type="date" 
               wire:model="answers.{{ $field['id'] }}"
               class="w-full bg-black/50 border border-gray-700 rounded p-3 text-white text-sm outline-none focus:border-cyan-500">
        @error("answers.{$field['id']}") <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
        @break

    @case('time')
        <input type="time" 
               wire:model="answers.{{ $field['id'] }}"
               class="w-full bg-black/50 border border-gray-700 rounded p-3 text-white text-sm outline-none focus:border-cyan-500">
        @error("answers.{$field['id']}") <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
        @break

    @case('datetime-local')
    @case('datetime')
        <input type="datetime-local" 
               wire:model="answers.{{ $field['id'] }}"
               class="w-full bg-black/50 border border-gray-700 rounded p-3 text-white text-sm outline-none focus:border-cyan-500">
        @error("answers.{$field['id']}") <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
        @break
@endswitch
