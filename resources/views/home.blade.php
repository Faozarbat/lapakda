<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#B9232D">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @include('partials.pwa-head')
    @include('partials.og-meta', [
        'ogTitle' => 'LapakDA — Katalog Online UMKM',
        'ogDescription' => 'Temukan resto favorit di sekitarmu. Pesan langsung via WhatsApp.',
        'ogImage' => asset('og-default.png'),
    ])

    <title>LapakDA - Katalog Online UMKM</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        [x-cloak] { display: none !important; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F9FA] text-gray-800 antialiased pb-28" 
      x-data="{ 
          toastShow: false, 
          toastMessage: '', 
          isLocating: false,
          cariRestoTerdekat() {
              if (!navigator.geolocation) {
                  this.toastMessage = 'Browser kamu tidak mendukung fitur lokasi.';
                  this.toastShow = true;
                  setTimeout(() => { this.toastShow = false; }, 3000);
                  return;
              }
              this.isLocating = true;
              navigator.geolocation.getCurrentPosition(
                  (position) => {
                      const lat = position.coords.latitude;
                      const lng = position.coords.longitude;
                      window.location.href = `{{ route('jelajah', 'resto') }}?lat=${lat}&lng=${lng}`;
                  },
                  (error) => {
                      this.toastMessage = 'Gagal mendapatkan lokasi. Pastikan izin GPS diaktifkan.';
                      this.toastShow = true;
                      setTimeout(() => { this.toastShow = false; }, 4000);
                      this.isLocating = false;
                  }
              );
          }
      }">

        <!-- BANNER INSTALL PWA (Gaya Iklan Atas) -->
    <div x-data="{ showBanner: false }" 
         @pwa-ready.window="showBanner = true" 
         x-show="showBanner" 
         x-transition.opacity
         x-cloak
         class="sticky top-0 z-[100] w-full bg-[#B9232D] text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-2 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-white text-[#B9232D] rounded-lg flex items-center justify-center font-extrabold text-sm flex-shrink-0">L</div>
                <div>
                    <p class="text-xs font-bold leading-tight">Install Aplikasi LapakDA</p>
                    <p class="text-[10px] text-red-100 hidden sm:block">Akses katalog lebih cepat & praktis di HP Anda.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <button @click="showBanner = false" class="text-xs font-semibold text-red-200 hover:text-white px-2">Tutup</button>
                <button @click="
                    if(window.deferredPrompt) {
                        window.deferredPrompt.prompt();
                        window.deferredPrompt.userChoice.then((choiceResult) => {
                            if (choiceResult.outcome === 'accepted') showBanner = false;
                            window.deferredPrompt = null;
                        });
                    } else {
                        // Fallback jika browser bandel (misal iOS Safari)
                        alert('Buka menu browser (titik tiga) lalu pilih \'Tambahkan ke Layar Utama\'.');
                    }
                " class="bg-white text-[#B9232D] px-3 py-1.5 rounded-lg text-xs font-extrabold shadow-sm hover:scale-105 transition-transform">Install</button>
            </div>
        </div>
    </div>

    <!-- HEADER -->
    <header class="bg-white sticky top-0 z-40 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-5 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#B9232D] flex items-center justify-center text-white font-extrabold text-xl shadow-sm">
                    L
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Lokasi Kamu</span>
                    <div class="flex items-center gap-1 text-sm font-bold text-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#B9232D]" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                        Batam
                    </div>
                </div>
            </div>
            
            <!-- Tombol Auth -->
            @auth
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 bg-red-50 text-[#B9232D] px-3 py-2 rounded-full text-xs font-bold hover:bg-[#B9232D] hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="flex items-center gap-2 bg-[#B9232D] text-white px-4 py-2 rounded-full text-xs font-bold shadow-md shadow-red-500/20 hover:scale-105 transition-transform">
                    Masuk / Daftar
                </a>
            @endauth
        </div>
    </header>

    @include('partials.security-banner')

    <main class="max-w-7xl mx-auto px-5 pt-5">
        
        <!-- Sapaan & Pencarian -->
        <div class="mb-6">
            <h1 class="text-[22px] font-extrabold text-gray-900 tracking-tight leading-tight mb-4">
                Mau cari apa <br>hari ini?
            </h1>
            
            <form action="{{ route('search.index') }}" method="GET" class="relative group mb-3">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-focus-within:text-[#B9232D] transition-colors" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="text" name="q" value="{{ request('q') }}"
                       class="block w-full pl-11 pr-4 py-3.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D] transition-all shadow-sm" 
                       placeholder="Cari nama resto atau makanan...">
                <button type="submit" class="hidden"></button>
            </form>
            
            <button @click="cariRestoTerdekat()" :disabled="isLocating" class="w-full flex items-center justify-center gap-2 bg-white border border-[#B9232D]/30 text-[#B9232D] py-3 rounded-xl text-sm font-bold shadow-sm hover:bg-red-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" x-show="!isLocating">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                </svg>
                <svg class="animate-spin h-4 w-4 text-[#B9232D]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" x-show="isLocating" x-cloak>
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="isLocating ? 'Mendeteksi lokasi...' : 'Cari Resto Terdekat dari Lokasimu'"></span>
            </button>
        </div>

        <!-- Banner Solid -->
        <div class="mb-8 relative rounded-2xl bg-[#B9232D] p-6 overflow-hidden shadow-md shadow-red-500/10">
            <div class="relative z-10 w-[70%]">
                <h2 class="text-lg font-bold text-white mb-1.5 leading-snug">Punya Usaha?</h2>
                <p class="text-red-100 text-xs mb-4">Gabung jadi mitra LapakDA. Gratis, tanpa biaya admin.</p>
                <a href="{{ route('store.create') }}" class="inline-block bg-white text-[#B9232D] px-4 py-2 rounded-lg text-xs font-bold hover:bg-gray-50 transition-colors">Daftar Sekarang</a>
            </div>
            <div class="absolute right-0 top-0 bottom-0 w-32 overflow-hidden">
                <svg class="absolute right-[-20%] top-[-20%] h-[140%] w-[140%] text-[#A01E27] opacity-50" viewBox="0 0 100 100" preserveAspectRatio="none" fill="currentColor"><circle cx="50" cy="50" r="50" /></svg>
                <svg class="absolute right-[-40%] bottom-[-40%] h-[120%] w-[120%] text-[#91121B] opacity-40" viewBox="0 0 100 100" preserveAspectRatio="none" fill="currentColor"><circle cx="50" cy="50" r="50" /></svg>
            </div>
        </div>

        <!-- Kategori Section -->
        <div class="mb-8">
            <div class="flex items-end justify-between mb-4">
                <h2 class="text-base font-extrabold text-gray-900">Jelajahi Kategori</h2>
            </div>
            
            <div class="flex overflow-x-auto space-x-3 pb-2 hide-scrollbar md:grid md:grid-cols-4 md:space-x-0 md:gap-4 md:pb-0">
                @foreach($kategoriList as $slug => $kategori)
                    @if($kategori['aktif'])
                        <a href="{{ route('jelajah', $slug) }}" class="flex-shrink-0 w-[85px] md:w-auto h-[105px] md:h-auto md:py-6 bg-white rounded-2xl flex flex-col items-center justify-center border border-gray-100 shadow-sm relative group transition-all hover:shadow-md">
                            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-8 h-1 bg-[#B9232D] rounded-b-md"></div>
                            <div class="w-11 h-11 md:w-14 md:h-14 bg-red-50 rounded-full flex items-center justify-center mb-2 group-hover:bg-[#B9232D] transition-colors duration-300">
                                @if($kategori['kategori_db'] === 'resto')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 text-[#B9232D] group-hover:text-white transition-colors" viewBox="0 0 20 20" fill="currentColor"><path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3z" /></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 text-[#B9232D] group-hover:text-white transition-colors" viewBox="0 0 20 20" fill="currentColor"><path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z" /><path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd" /></svg>
                                @endif
                            </div>
                            <span class="font-bold text-gray-900 text-xs md:text-sm">{{ $kategori['label'] }}</span>
                            @if(isset($kategori['jumlah']))
                                <span class="text-[10px] text-gray-500 mt-0.5">{{ $kategori['jumlah'] }} Toko</span>
                            @endif
                        </a>
                    @else
                        <a href="{{ route('jelajah', $slug) }}" class="flex-shrink-0 w-[85px] md:w-auto h-[105px] md:h-auto md:py-6 bg-gray-50 rounded-2xl flex flex-col items-center justify-center border border-gray-100 border-dashed relative group">
                            <div class="w-11 h-11 md:w-14 md:h-14 bg-white rounded-full flex items-center justify-center mb-2 shadow-sm opacity-60">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                            </div>
                            <span class="font-bold text-gray-400 text-xs md:text-sm">{{ $kategori['label'] }}</span>
                            <span class="text-[9px] font-bold text-orange-500 bg-orange-50 px-1.5 py-0.5 rounded mt-1">Segera</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </main>

    <!-- BOTTOM NAVIGATION -->
    <div class="fixed bottom-6 left-4 right-4 z-40 flex justify-center pointer-events-none">
        @include('layouts.navigation')
    </div>

        

    <!-- TOAST NOTIFICATION -->
    <div x-show="toastShow" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed top-20 left-4 right-4 z-[60] flex justify-center pointer-events-none"
         x-cloak>
        <div class="bg-gray-900 text-white px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 w-full max-w-sm pointer-events-auto">
            <div class="bg-red-500/20 p-1.5 rounded-lg flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <p class="text-sm font-medium flex-1" x-text="toastMessage"></p>
            <button @click="toastShow = false" class="text-gray-400 hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
            </button>
        </div>
    </div>

    @guest
    <!-- GUEST POPUP -->
    <div x-data="{ 
             showGuestPopup: false,
             isSubmitting: false,
             guestName: '',
             guestPhone: '',
             errorMsg: '',
             init() {
                 const promptedUntil = localStorage.getItem('lapakda_guest_prompted_until');
                 if (!promptedUntil || Date.now() > parseInt(promptedUntil)) {
                     setTimeout(() => { this.showGuestPopup = true }, 1500);
                 }
             },
             closePopup() {
                 this.showGuestPopup = false;
                 const nextPrompt = Date.now() + (2 * 60 * 60 * 1000); // 2 Jam
                 localStorage.setItem('lapakda_guest_prompted_until', nextPrompt);
             },
             submitGuestForm() {
                 if(this.isSubmitting) return;
                 this.isSubmitting = true;
                 this.errorMsg = '';

                 const csrfToken = document.querySelector('meta[name=csrf-token]')?.content;

                 fetch('{{ route('guest.register') }}', {
                     method: 'POST',
                     headers: { 
                         'Content-Type': 'application/json', 
                         'X-CSRF-TOKEN': csrfToken,
                         'Accept': 'application/json'
                     },
                     body: JSON.stringify({ 
                         name: this.guestName, 
                         nomor_hp: this.guestPhone 
                     })
                 })
                 .then(res => res.json())
                 .then(data => {
                     if(data.success) {
                         window.location.reload(); 
                     } else {
                         this.errorMsg = data.message; 
                         this.isSubmitting = false;
                     }
                 })
                 .catch(err => {
                     console.error(err);
                     this.isSubmitting = false;
                 });
             }
         }" 
         x-show="showGuestPopup" 
         x-cloak 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closePopup()"></div>

        <div class="bg-white rounded-3xl w-full max-w-sm relative z-10 p-6 shadow-2xl"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95">
            
            <button @click="closePopup()" class="absolute top-4 right-4 w-8 h-8 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 hover:bg-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
            </button>

            <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                👋
            </div>

            <h3 class="text-xl font-extrabold text-gray-900 text-center mb-2">Kenalan Dulu Yuk!</h3>
            <p class="text-sm text-gray-500 text-center mb-6 leading-relaxed">
                Isi nama dan nomor WA kamu biar pesanan otomatis tersimpan di Riwayat dan kamu dapat update promo menarik.
            </p>

            <form @submit.prevent="submitGuestForm()" class="space-y-3">
                <div>
                    <input type="text" x-model="guestName" placeholder="Nama Lengkap" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]" required>
                </div>
                <div>
                    <input type="tel" x-model="guestPhone" placeholder="Nomor WhatsApp (cth: 0812...)" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]" required>
                </div>
                
                <div x-show="errorMsg" class="text-red-500 text-xs font-bold text-center bg-red-50 p-2 rounded-lg" x-text="errorMsg" x-cloak></div>

                <button type="submit" :disabled="isSubmitting" class="w-full bg-[#B9232D] text-white py-3.5 rounded-xl text-sm font-bold shadow-lg shadow-red-500/30 hover:scale-[1.02] transition-transform mt-2 disabled:opacity-50 disabled:hover:scale-100 flex justify-center items-center">
                    <span x-show="!isSubmitting">Simpan & Lanjutkan</span>
                    <span x-show="isSubmitting" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Memproses...
                    </span>
                </button>
            </form>

            <button @click="closePopup()" class="w-full text-center text-xs font-semibold text-gray-400 mt-4 hover:text-gray-600 transition-colors">
                Nanti aja, lanjut sebagai tamu
            </button>
        </div>
    </div>
    @endguest

    <script>
        // PWA DETECTOR
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            window.deferredPrompt = e;
            window.dispatchEvent(new Event('pwa-ready'));
        });

        // PAKSA MUNCUL: Jika 2 detik tidak muncul, kita paksa muncul untuk testing
        window.setTimeout(() => {
            if (!window.deferredPrompt) {
                window.dispatchEvent(new Event('pwa-ready'));
            }
        }, 2000);
    </script>
</body>
</html>