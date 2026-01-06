<!-- DYNAMIC FIELDS (Alpine-managed) -->
<div class="space-y-4 pb-20">
    <template x-if="!fields.length">
        <div class="bg-[#1a1a1a] p-12 rounded-xl border border-dashed border-gray-700 text-center">
            <p class="text-gray-500">Belum ada field custom</p>
        </div>
    </template>

    <template x-for="(field, index) in fields" :key="field.id">
        <div class="bg-[#1a1a1a] p-5 rounded-xl border border-gray-800 shadow-md hover:border-l-4 hover:border-l-cyan-500 transition-all">

            <!-- SECTION -->
            <template x-if="field.type === 'section'">
                <div class="bg-gradient-to-r from-cyan-900/30 to-blue-900/30 -m-5 p-5 rounded-xl border-2 border-dashed border-cyan-500/50">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-2 text-cyan-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                            <span class="text-xs font-bold uppercase tracking-widest">Bagian / Section</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="moveFieldUp(index)" class="p-1 text-gray-500 hover:text-cyan-400 cursor-pointer" :class="index === 0 ? 'opacity-30 !cursor-not-allowed' : ''" :disabled="index === 0" title="Pindah ke atas">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </button>
                            <button type="button" @click="moveFieldDown(index)" class="p-1 text-gray-500 hover:text-cyan-400 cursor-pointer" :class="index === fields.length - 1 ? 'opacity-30 !cursor-not-allowed' : ''" :disabled="index === fields.length - 1" title="Pindah ke bawah">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <button type="button" @click="removeField(index)" class="p-1 text-gray-500 hover:text-red-500 cursor-pointer" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                    <input type="text" x-model="field.label" class="w-full bg-transparent border-b-2 border-cyan-500/50 text-xl font-bold text-white placeholder-gray-500 outline-none pb-2 focus:border-cyan-400 transition" placeholder="Judul Bagian">
                    <textarea x-model="field.description" rows="2" class="w-full bg-transparent border-b border-gray-700 text-sm text-gray-400 placeholder-gray-600 outline-none resize-none mt-3 focus:border-cyan-500 transition" placeholder="Deskripsi bagian (opsional)"></textarea>
                    <p class="text-[10px] text-cyan-500/70 mt-3 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Pertanyaan setelah ini akan muncul di halaman baru
                    </p>
                </div>
            </template>

            <!-- STATIC BLOCKS -->
            <template x-if="['note','image-view','link'].includes(field.type)">
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-cyan-500 text-xs font-bold uppercase tracking-widest mb-2">
                        <span class="flex items-center gap-2">
                            <template x-if="field.type === 'note'"><span class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>Teks / Judul</span></template>
                            <template x-if="field.type === 'image-view'"><span class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>Gambar Banner</span></template>
                            <template x-if="field.type === 'link'"><span class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>Link Eksternal</span></template>
                        </span>
                        <button type="button" @click="removeField(index)" class="text-gray-600 hover:text-red-500 cursor-pointer" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>

                    <input type="text" x-model="field.label" class="w-full bg-transparent border-b border-gray-700 p-2 text-lg font-bold text-white outline-none focus:border-cyan-500" :placeholder="field.type === 'image-view' ? 'Judul Gambar (Opsional)' : 'Judul / Label'">

                    <template x-if="field.type === 'note'">
                        <textarea x-model="field.description" rows="3" class="w-full bg-black/50 border border-gray-700 rounded p-2 text-sm text-white outline-none h-20 focus:border-cyan-500" placeholder="Isi teks..."></textarea>
                    </template>
                    <template x-if="field.type === 'image-view'">
                        <div class="space-y-2" x-data="{ showFullscreen: false }">
                            <label class="flex items-center gap-2 px-3 py-2 bg-black border border-gray-700 rounded cursor-pointer hover:border-cyan-500 transition w-fit">
                                <svg class="w-4 h-4 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                <span class="text-xs text-gray-300">Upload Gambar Banner</span>
                                <input type="file" accept="image/*" class="hidden" @change="handleBannerUpload($event, field)">
                            </label>
                            <template x-if="field.content">
                                <div class="relative w-fit group">
                                    <img :src="field.content" class="h-24 rounded border border-gray-700 object-cover cursor-pointer" alt="Preview" @click="showFullscreen = true">
                                    <div class="absolute top-1 right-1 flex gap-1 opacity-0 group-hover:opacity-100 transition">
                                        <button type="button" @click="showFullscreen = true" class="p-1 bg-black/70 rounded text-white hover:bg-black transition cursor-pointer" title="Fullscreen">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                        </button>
                                        <a :href="field.content" target="_blank" class="p-1 bg-black/70 rounded text-white hover:bg-black transition cursor-pointer" title="Buka di tab baru" @click.stop>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </div>
                                    <button type="button" @click="field.content = ''" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 cursor-pointer hover:bg-red-600 transition" title="Hapus gambar">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    <template x-teleport="body">
                                        <div x-show="showFullscreen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showFullscreen = false" @keydown.escape.window="showFullscreen = false" class="fixed inset-0 z-[99999] bg-black flex items-center justify-center p-4 cursor-pointer">
                                            <div class="absolute top-4 left-1/2 -translate-x-1/2 flex gap-2 bg-black/80 backdrop-blur-sm px-3 py-2 rounded-full border border-gray-700">
                                                <a :href="field.content" target="_blank" @click.stop class="p-2 text-white hover:text-cyan-400 transition cursor-pointer" title="Buka di tab baru">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </a>
                                                <button type="button" @click.stop="showFullscreen = false" class="p-2 text-white hover:text-red-400 transition cursor-pointer" title="Tutup (ESC)">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>
                                            <img :src="field.content" class="max-w-full max-h-[90vh] object-contain cursor-default" @click.stop>
                                        </div>
                                    </template>
                                </div>
                            </template>
                    </template>

                    <template x-if="!['section','note','image-view','link'].includes(field.type)">
                        <div class="space-y-4">
                            <div class="flex flex-col md:flex-row gap-4">
                                <div class="flex-1">
                                    <input type="text" x-model="field.label" class="w-full bg-[#222] border-b border-gray-700 p-3 text-base text-white outline-none focus:border-cyan-500 rounded-t" placeholder="Pertanyaan">
                                </div>
                                <div class="w-full md:w-40">
                                    <select x-model="field.type" @change="updateFieldType(index, field.type)" class="w-full bg-[#222] border border-gray-700 rounded p-2 text-sm text-gray-300 focus:border-cyan-500 outline-none">
                                        <option value="text">Jawaban Singkat</option>
                                        <option value="textarea">Paragraf</option>
                                        <option value="radio">Pilihan Ganda</option>
                                        <option value="checkbox">Kotak Centang</option>
                                        <option value="select">Dropdown</option>
                                        <option value="file">Upload File</option>
                                        <option value="date">Tanggal</option>
                                        <option value="time">Waktu</option>
                                        <option value="datetime-local">Tanggal & Waktu</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex flex-col gap-3 bg-[#111] p-3 rounded border border-gray-800" x-data="mediaEmbedder(field)">
                                <div class="flex items-center gap-3">
                                    <span class="text-[10px] text-gray-500 font-bold uppercase mr-2">Sisipkan:</span>
                                    <button type="button" @click="toggleImage()" class="p-1 rounded transition cursor-pointer" :class="imageUrl || showImage ? 'text-cyan-400 bg-cyan-900/30' : 'text-gray-500 hover:text-white'" title="Sisipkan Gambar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </button>
                                    <button type="button" @click="toggleLink()" class="p-1 rounded transition cursor-pointer" :class="showLink ? 'text-cyan-400 bg-cyan-900/30' : 'text-gray-500 hover:text-white'" title="Sisipkan Link">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                    </button>
                                </div>

                                <div class="space-y-3">
                                    <div x-show="showImage || imageUrl" x-cloak class="flex flex-col gap-2 animate-fade-in bg-black/30 p-2 rounded border border-gray-800">
                                        <div class="flex justify-between items-center">
                                            <label class="flex items-center gap-2 cursor-pointer bg-black border border-gray-700 rounded px-2 py-1 w-fit hover:border-cyan-500">
                                                <svg class="w-3 h-3 text-gray-400" x-show="!uploading" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                <svg class="w-3 h-3 text-cyan-400 animate-spin" x-show="uploading" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                <span class="text-[10px] text-gray-300" x-text="uploading ? 'Uploading...' : 'Pilih Gambar'"></span>
                                                <input type="file" accept="image/*" class="hidden" @change="handleImageUpload($event)">
                                            </label>
                                            <button type="button" @click="clearImageData()" class="text-gray-600 hover:text-red-500 cursor-pointer p-1" title="Hapus gambar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                        <template x-if="imageUrl">
                                            <div class="relative w-fit group">
                                                <img :src="imageUrl" class="h-24 rounded border border-gray-600 object-cover w-fit cursor-pointer" alt="Preview" @click="showEmbedFullscreen = true">
                                                <div class="absolute top-1 right-1 flex gap-1 opacity-0 group-hover:opacity-100 transition">
                                                    <button type="button" @click="showEmbedFullscreen = true" class="p-1 bg-black/70 rounded text-white hover:bg-black transition cursor-pointer" title="Fullscreen">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                                    </button>
                                                    <a :href="imageUrl" target="_blank" class="p-1 bg-black/70 rounded text-white hover:bg-black transition cursor-pointer" title="Buka di tab baru" @click.stop>
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                    </a>
                                                </div>
                                                <template x-teleport="body">
                                                    <div x-show="showEmbedFullscreen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showEmbedFullscreen = false" @keydown.escape.window="showEmbedFullscreen = false" class="fixed inset-0 z-[99999] bg-black flex items-center justify-center p-4 cursor-pointer">
                                                        <div class="absolute top-4 left-1/2 -translate-x-1/2 flex gap-2 bg-black/80 backdrop-blur-sm px-3 py-2 rounded-full border border-gray-700">
                                                            <a :href="imageUrl" target="_blank" @click.stop class="p-2 text-white hover:text-cyan-400 transition cursor-pointer" title="Buka di tab baru">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                            </a>
                                                            <button type="button" @click.stop="showEmbedFullscreen = false" class="p-2 text-white hover:text-red-400 transition cursor-pointer" title="Tutup (ESC)">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                            </button>
                                                        </div>
                                                        <img :src="imageUrl" class="max-w-full max-h-[90vh] object-contain cursor-default" @click.stop>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>

                                    <div x-show="showLink" x-cloak class="flex gap-2 animate-fade-in bg-black/30 p-2 rounded border border-gray-800 items-center">
                                        <div class="flex-1 flex flex-col gap-2">
                                            <input type="text" x-model="field.linkUrl" class="bg-black text-xs text-white border border-gray-700 rounded px-2 py-1 outline-none focus:border-cyan-500" placeholder="URL Link (https://...)">
                                            <input type="text" x-model="field.linkText" class="bg-black text-xs text-white border border-gray-700 rounded px-2 py-1 outline-none focus:border-cyan-500" placeholder="Teks Link (Optional)">
                                        </div>
                                        <button type="button" @click="clearLinkData()" class="text-gray-600 hover:text-red-500 cursor-pointer p-1" title="Hapus link">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <input type="text" x-model="field.description" class="w-full bg-transparent border-b border-dashed border-gray-800 text-xs text-gray-500 focus:text-white p-1 outline-none" placeholder="Deskripsi (Opsional)">

                            <template x-if="field.type === 'file'">
                                <div class="ml-2 mt-4 space-y-4 border-l-2 border-cyan-500 pl-4 bg-cyan-900/10 p-4 rounded-r-lg" x-data="fileSettings(field)">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-300">Batasi jenis file</span>
                                        <button type="button" @click="toggleRestrict()" class="relative inline-flex h-5 w-10 items-center rounded-full transition cursor-pointer" :class="restrictTypes ? 'bg-cyan-600' : 'bg-gray-600'">
                                            <span class="inline-block h-3 w-3 transform rounded-full bg-white transition" :class="restrictTypes ? 'translate-x-6' : 'translate-x-1'"></span>
                                        </button>
                                    </div>
                                    <div x-show="restrictTypes" x-cloak class="grid grid-cols-2 gap-3">
                                        <template x-for="ft in fileTypeOptions" :key="ft.id">
                                            <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-400 hover:text-white">
                                                <input type="checkbox" @click="toggleType(ft.id)" :checked="allowedTypes.includes(ft.id)" class="accent-cyan-500 w-4 h-4 rounded">
                                                <span x-text="ft.label"></span>
                                            </label>
                                        </template>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-300">Max size</span>
                                        <select x-model="field.fileSettings.maxSize" class="bg-black border border-gray-700 rounded p-1 text-sm text-white">
                                            <option value="524288">500 KB</option>
                                            <option value="1048576">1 MB</option>
                                            <option value="2097152">2 MB</option>
                                            <option value="5242880">5 MB</option>
                                        </select>
                                    </div>
                                </div>
                            </template>

                            <template x-if="['radio','checkbox','select'].includes(field.type)">
                                <div class="ml-2 space-y-2 pl-2 border-l-2 border-gray-800">
                                    <template x-for="(option, optIndex) in field.options" :key="optIndex">
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-600 text-xs" x-text="(optIndex + 1) + '.'"></span>
                                            <input type="text" x-model="field.options[optIndex]" class="bg-transparent border-b border-gray-800 hover:border-gray-600 focus:border-cyan-500 text-sm text-gray-300 w-full p-1 outline-none" :placeholder="'Opsi ' + (optIndex + 1)">
                                            <button type="button" @click="removeOption(index, optIndex)" class="text-gray-600 hover:text-red-500 cursor-pointer" title="Hapus opsi"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                                        </div>
                                    </template>
                                    <button type="button" @click="addOption(index)" class="text-cyan-500 text-xs hover:underline flex items-center gap-1 mt-2 p-1 cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Tambah Opsi
                                    </button>
                                </div>
                            </template>

                            <div class="flex justify-between items-center gap-4 pt-4 border-t border-gray-800 mt-4">
                                <div class="flex items-center gap-1">
                                    <button type="button" @click="moveFieldUp(index)" class="p-1.5 rounded transition cursor-pointer" :class="index === 0 ? 'text-gray-700 !cursor-not-allowed' : 'text-gray-500 hover:text-cyan-400 hover:bg-cyan-900/20'" :disabled="index === 0" title="Pindah ke atas">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    </button>
                                    <button type="button" @click="moveFieldDown(index)" class="p-1.5 rounded transition cursor-pointer" :class="index === fields.length - 1 ? 'text-gray-700 !cursor-not-allowed' : 'text-gray-500 hover:text-cyan-400 hover:bg-cyan-900/20'" :disabled="index === fields.length - 1" title="Pindah ke bawah">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-2 border-r border-gray-700 pr-4">
                                        <span class="text-xs text-gray-500 cursor-pointer" @click="toggleRequired(field)">Wajib</span>
                                        <button type="button" @click="toggleRequired(field)" class="w-8 h-4 rounded-full cursor-pointer relative transition" :class="field.required ? 'bg-cyan-600' : 'bg-gray-700'">
                                            <div class="w-2 h-2 bg-white rounded-full absolute top-1 transition-all" :class="field.required ? 'left-5' : 'left-1'"></div>
                                        </button>
                                    </div>
                                    <button type="button" @click="duplicateField(index)" class="text-gray-500 hover:text-white p-2 cursor-pointer" title="Duplikasi"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></button>
                                    <button type="button" @click="removeField(index)" class="text-gray-500 hover:text-red-500 p-2 cursor-pointer" title="Hapus"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
