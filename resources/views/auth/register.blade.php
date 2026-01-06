<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar - MIRAI Hub</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 130 85'%3E%3Cpath d='M0,80 L20,0 L45,0 L25,80 Z' fill='%2306b6d4'/%3E%3Cpath d='M85,80 L105,0 L130,0 L110,80 Z' fill='%233b82f6'/%3E%3Cpath d='M54,85 L34,25 L54,25 L64,50 L84,25 L104,25 Z' fill='white'/%3E%3C/svg%3E">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#050505] text-zinc-100 min-h-screen flex items-center justify-center p-4 grid-bg">
    <!-- Glow Effect -->
    <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[400px] h-[400px] bg-blue-500/10 rounded-full blur-[100px] pointer-events-none"></div>
    
    <div class="w-full max-w-md relative z-10">
        <div class="text-center mb-8">
            <a href="/" class="inline-block">
                <x-mirai-logo size="lg" />
            </a>
        </div>
        
        <div class="bg-[#111] border border-gray-800 rounded-xl p-8">
            <h1 class="text-2xl font-display font-bold mb-2 text-center">Buat Akun MIRAI</h1>
            <p class="text-gray-500 text-sm text-center mb-6">Daftar untuk ikut turnamen</p>
            
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <!-- Error message container for Firebase -->
            <div id="firebase-error" class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400 text-sm hidden"></div>
            
            <!-- Google Register Button (Firebase) -->
            <button type="button" id="google-login-btn" class="w-full flex items-center justify-center gap-3 py-3 bg-white text-gray-800 rounded-lg font-semibold hover:bg-gray-100 transition mb-6 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                <span id="google-btn-text">Daftar dengan Google</span>
            </button>
            
            <div class="relative mb-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-700"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-[#111] text-gray-500">atau daftar manual</span>
                </div>
            </div>
            
            <form method="POST" action="{{ route('auth.register.submit') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <!-- Avatar Upload -->
                <div class="flex justify-center mb-2">
                    <div x-data="{ preview: null }" class="relative">
                        <div class="w-24 h-24 rounded-full bg-[#222] border-2 border-gray-700 overflow-hidden flex items-center justify-center">
                            <template x-if="preview">
                                <img :src="preview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!preview">
                                <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </template>
                        </div>
                        <label class="absolute bottom-0 right-0 w-8 h-8 bg-cyan-500 rounded-full flex items-center justify-center cursor-pointer hover:bg-cyan-600 transition">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <input type="file" name="avatar" accept="image/*" class="hidden" 
                                   @change="preview = URL.createObjectURL($event.target.files[0])">
                        </label>
                    </div>
                </div>
                <p class="text-center text-xs text-gray-500 mb-4">Upload foto profil (opsional)</p>
                
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Nama Lengkap <span class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full px-4 py-3 bg-[#222] border border-gray-700 rounded-lg focus:outline-none focus:border-cyan-500 transition"
                           placeholder="Nama kamu">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Email <span class="text-red-400">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-3 bg-[#222] border border-gray-700 rounded-lg focus:outline-none focus:border-cyan-500 transition"
                           placeholder="email@example.com">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">No. WhatsApp <span class="text-red-400">*</span></label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" required
                           class="w-full px-4 py-3 bg-[#222] border border-gray-700 rounded-lg focus:outline-none focus:border-cyan-500 transition"
                           placeholder="08xxxxxxxxxx">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Game Favorit</label>
                        <select name="game_type" class="w-full px-4 py-3 bg-[#222] border border-gray-700 rounded-lg focus:outline-none focus:border-cyan-500 transition appearance-none">
                            <option value="">Pilih Game</option>
                            <option value="Mobile Legends" {{ old('game_type') == 'Mobile Legends' ? 'selected' : '' }}>Mobile Legends</option>
                            <option value="Valorant" {{ old('game_type') == 'Valorant' ? 'selected' : '' }}>Valorant</option>
                            <option value="PUBG Mobile" {{ old('game_type') == 'PUBG Mobile' ? 'selected' : '' }}>PUBG Mobile</option>
                            <option value="Free Fire" {{ old('game_type') == 'Free Fire' ? 'selected' : '' }}>Free Fire</option>
                            <option value="Other" {{ old('game_type') == 'Other' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">ID Game</label>
                        <input type="text" name="game_id" value="{{ old('game_id') }}"
                               class="w-full px-4 py-3 bg-[#222] border border-gray-700 rounded-lg focus:outline-none focus:border-cyan-500 transition"
                               placeholder="123456789">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Password <span class="text-red-400">*</span></label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 bg-[#222] border border-gray-700 rounded-lg focus:outline-none focus:border-cyan-500 transition"
                           placeholder="Min. 8 karakter">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Konfirmasi Password <span class="text-red-400">*</span></label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-3 bg-[#222] border border-gray-700 rounded-lg focus:outline-none focus:border-cyan-500 transition"
                           placeholder="Ulangi password">
                </div>
                
                <button type="submit" class="w-full py-3 bg-gradient-to-r from-cyan-500 to-blue-600 rounded font-display font-bold hover:opacity-90 transition shadow-[0_0_20px_rgba(6,182,212,0.3)] cursor-pointer">
                    Daftar
                </button>
            </form>
            
            <p class="mt-6 text-center text-sm text-gray-500">
                Sudah punya akun? <a href="{{ route('login') }}" class="text-cyan-400 hover:underline cursor-pointer">Login</a>
            </p>
        </div>
    </div>

    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-auth-compat.js"></script>
    
    <script>
        // Firebase configuration
        const firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key') }}",
            authDomain: "{{ config('services.firebase.auth_domain') }}",
            projectId: "{{ config('services.firebase.project_id') }}",
        };

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        
        const googleBtn = document.getElementById('google-login-btn');
        const googleBtnText = document.getElementById('google-btn-text');
        const errorDiv = document.getElementById('firebase-error');
        
        function showError(message) {
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
        }
        
        function hideError() {
            errorDiv.classList.add('hidden');
        }
        
        function setLoading(loading) {
            googleBtn.disabled = loading;
            googleBtnText.textContent = loading ? 'Memproses...' : 'Daftar dengan Google';
        }
        
        googleBtn.addEventListener('click', async () => {
            hideError();
            setLoading(true);
            
            try {
                const provider = new firebase.auth.GoogleAuthProvider();
                provider.setCustomParameters({
                    prompt: 'select_account'
                });
                
                const result = await firebase.auth().signInWithPopup(provider);
                const idToken = await result.user.getIdToken();
                
                // Send token to backend for verification
                const response = await fetch('{{ route("auth.firebase.verify") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ idToken }),
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    showError(data.message || 'Pendaftaran gagal');
                    setLoading(false);
                }
                
            } catch (error) {
                console.error('Firebase Auth Error:', error);
                
                let message = 'Terjadi kesalahan saat mendaftar';
                if (error.code === 'auth/popup-closed-by-user') {
                    message = 'Pendaftaran dibatalkan';
                } else if (error.code === 'auth/network-request-failed') {
                    message = 'Koneksi internet bermasalah';
                } else if (error.message) {
                    message = error.message;
                }
                
                showError(message);
                setLoading(false);
            }
        });
    </script>
</body>
</html>
