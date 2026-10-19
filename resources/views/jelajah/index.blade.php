<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#B9232D">
    
    @include('partials.pwa-head')
    @include('partials.og-meta', [
        'ogTitle' => $kategori['label'] . ' — LapakDA',
        'ogDescription' => 'Jelajahi ' . strtolower($kategori['label']) . ' UMKM di LapakDA.',
        'ogImage' => asset('og-default.png'),
    ])

    <title>{{ $kategori['label'] }} — LapakDA</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        [x-cloak] { display: none !important; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F9FA] text-gray-800 antialiased pb-32" 
      x-data="{ 
          filterOpen: false,
          toastShow: false, 
          toastMessage: '', 
          isLocating: false,
          cariTerdekat() {
              if (!navigator.geolocation) {
                  this.toastMessage = 'Browser kamu tidak mendukung deteksi lokasi.';
                  this.toastShow = true;
                  setTimeout(() => { this.toastShow = false; }, 3000);
                  return;
              }
              this.isLocating = true;
              navigator.geolocation.getCurrentPosition(
                  (pos) => {
                      const params = new URLSearchParams(window.location.search);
                      params.set('lat', pos.coords.latitude);
                      params.set('lng', pos.coords.longitude);
                      window.location.href = window.location.pathname + '?' + params.toString();
                  },
                  () => {
                      this.toastMessage = 'Gagal mengambil lokasi. Pastikan izin GPS diaktifkan.';
                      this.toastShow = true;
                      setTimeout(() => { this.toastShow = false; }, 4000);
                      this.isLocating = false;
                  }
              );
          }
      }">

    <!-- HEADER -->
    <header class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-700 hover:bg-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
            </a>
            <h1 class="font-bold text-base text-gray-900 absolute left-1/2 -translate-x-1/2">{{ $kategori['label'] }}</h1>
            
            <!-- Tombol Auth -->
            @auth
                <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-[#B9232D] hover:bg-[#B9232D] hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>
                </a>
            @else
                <a href="{{ route('login') }}" class="flex items-center justify-center px-3 h-10 rounded-full bg-[#B9232D] text-white text-xs font-bold shadow-md shadow-red-500/20">
                    Masuk
                </a>
            @endauth
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 pt-4">
        
        <!-- ACTION BAR (Filter & Terdekat - Lebih Ringkas) -->
        <div class="grid grid-cols-2 gap-2 mb-4">
            
            <!-- Tombol Buka Bottom Sheet Filter -->
            <button @click="filterOpen = true" class="flex items-center justify-center gap-1.5 bg-white border border-gray-200 text-gray-700 py-2.5 rounded-xl text-xs font-bold shadow-sm hover:bg-gray-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                </svg>
                <span class="truncate max-w-[120px]">
                    {{ $filterKecamatan ? $kecamatans->firstWhere('id', $filterKecamatan)->nama : 'Kecamatan' }}
                </span>
            </button>

            <!-- Tombol Cari Terdekat -->
            @if ($urutBerdasarkanJarak)
                <a href="{{ route('jelajah', array_merge(['kategoriSlug' => $kategoriSlug], request()->except(['lat', 'lng']))) }}" class="flex items-center justify-center gap-1.5 bg-red-50 border border-[#B9232D]/20 text-[#B9232D] py-2.5 rounded-xl text-xs font-bold shadow-sm hover:bg-red-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Reset Jarak
                </a>
            @else
                <button @click="cariTerdekat()" :disabled="isLocating" class="flex items-center justify-center gap-1.5 bg-[#B9232D] text-white py-2.5 rounded-xl text-xs font-bold shadow-md shadow-red-500/20 hover:scale-[1.02] transition-transform disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" x-show="!isLocating">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                    </svg>
                    <svg class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" x-show="isLocating" x-cloak>
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="isLocating ? 'Mencari...' : 'Terdekat'"></span>
                </button>
            @endif
        </div>

        <!-- Info Banner Jarak -->
        @if ($urutBerdasarkanJarak)
            <div class="bg-orange-50 border border-orange-100 rounded-xl p-2.5 mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-orange-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                <p class="text-[11px] text-orange-700 font-medium">Menampilkan dari yang terdekat.</p>
            </div>
        @endif

        <!-- Daftar Toko (GRID 2 KOLOM UNTUK MOBILE) -->
        @if ($stores->isEmpty())
            <div class="text-center bg-white rounded-3xl p-8 border border-gray-100 shadow-sm mt-4">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900 mb-1">Belum Ada {{ $kategori['label'] }}</h3>
                <p class="text-xs text-gray-500 mb-4">Tidak ada yang cocok dengan filter ini.</p>
                <a href="{{ route('register') }}" class="inline-block bg-[#B9232D] text-white px-4 py-2 rounded-lg text-xs font-bold shadow-md hover:scale-105 transition-transform">Daftar Toko</a>
            </div>
        @else
            <!-- Grid Utama -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-8">
                @foreach ($stores as $store)
                    <a href="{{ route('store.show', [$store->kecamatan->slug, $store->slug]) }}" class="bg-white rounded-2xl border border-gray-100 shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 group flex flex-col">
                        
                        <!-- Thumbnail (Aspect Ratio 4:3 agar proporsional) -->
                        <div class="w-full aspect-[4/3] bg-gray-100 relative overflow-hidden flex items-center justify-center">
                            @if($store->foto_profil)
                                <img src="{{ Storage::url($store->foto_profil) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $store->nama }}">
                            @elseif($store->foto_background)
                                <img src="{{ Storage::url($store->foto_background) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $store->nama }}">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-red-50 to-red-100 flex items-center justify-center">
                                    <span class="text-4xl opacity-40 group-hover:scale-110 group-hover:opacity-60 transition-all duration-500">
                                        {{ $kategori['icon'] }}
                                    </span>
                                </div>
                            @endif
                            
                            <!-- Status Buka -->
                            <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm px-1.5 py-0.5 rounded text-[9px] font-bold text-gray-700 flex items-center gap-1 shadow-sm">
                                <span class="w-1 h-1 rounded-full bg-green-500"></span> Buka
                            </div>
                        </div>
                        
                        <!-- Info Card -->
                        <div class="p-2.5 flex-grow flex flex-col">
                            <h3 class="font-bold text-gray-900 text-sm leading-tight line-clamp-2 mb-1">{{ $store->nama }}</h3>
                            
                            <p class="text-[10px] text-gray-500 flex items-center gap-0.5 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                                {{ $store->kecamatan->nama }}
                            </p>
                            
                            <!-- Footer Card (Jarak / Icon Panah) -->
                            <div class="mt-auto pt-2 flex items-center justify-between border-t border-gray-50">
                                @if ($urutBerdasarkanJarak && isset($store->jarak_km))
                                    <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-[#B9232D] bg-red-50 px-1.5 py-0.5 rounded">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                                        {{ number_format($store->jarak_km, 1) }} km
                                    </span>
                                @else
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Lihat Menu</span>
                                @endif
                                <span class="text-[#B9232D]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Paginasi (Lebih Ringkas) -->
            @if ($stores->hasPages())
                <div class="flex items-center justify-center gap-2 mb-8">
                    @if ($stores->onFirstPage())
                        <span class="px-3 py-1.5 bg-gray-100 text-gray-400 text-xs font-bold rounded-lg cursor-not-allowed">&laquo;</span>
                    @else
                        <a href="{{ $stores->previousPageUrl() }}" class="px-3 py-1.5 bg-white border border-gray-200 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-50 transition-colors">&laquo;</a>
                    @endif

                    <span class="px-3 py-1.5 bg-[#B9232D] text-white text-xs font-bold rounded-lg shadow-sm">
                        {{ $stores->currentPage() }} / {{ $stores->lastPage() }}
                    </span>

                    @if ($stores->hasMorePages())
                        <a href="{{ $stores->nextPageUrl() }}" class="px-3 py-1.5 bg-white border border-gray-200 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-50 transition-colors">&raquo;</a>
                    @else
                        <span class="px-3 py-1.5 bg-gray-100 text-gray-400 text-xs font-bold rounded-lg cursor-not-allowed">&raquo;</span>
                    @endif
                </div>
            @endif
        @endif
    </main>

    <!-- BOTTOM NAVIGATION (Dikembalikan!) -->
    <div class="fixed bottom-6 left-4 right-4 z-40 flex justify-center pointer-events-none">
        @include('layouts.navigation')
    </div>

    <!-- BOTTOM SHEET FILTER KECAMATAN -->
    <form id="form-filter" method="GET" action="{{ route('jelajah', $kategoriSlug) }}">
        <input type="hidden" name="kecamatan_id" id="input-kecamatan" value="{{ $filterKecamatan }}">
        @if ($urutBerdasarkanJarak)
            <input type="hidden" name="lat" value="{{ request('lat') }}">
            <input type="hidden" name="lng" value="{{ request('lng') }}">
        @endif
    </form>

    <div x-show="filterOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="filterOpen = false"
         class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[60]"
         x-cloak></div>

    <div x-show="filterOpen"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="fixed bottom-0 left-0 right-0 z-[70] bg-white rounded-t-3xl shadow-2xl pb-[env(safe-area-inset-bottom)]"
         x-cloak>
        
        <div class="w-full flex justify-center pt-3 pb-2" @click="filterOpen = false">
            <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
        </div>

        <div class="px-5 pb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-extrabold text-gray-900">Pilih Kecamatan</h3>
                <button @click="filterOpen = false" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                </button>
            </div>

            <div class="max-h-[60vh] overflow-y-auto space-y-1.5 pr-1 hide-scrollbar">
                <button @click="document.getElementById('input-kecamatan').value = ''; document.getElementById('form-filter').submit();" 
                        class="w-full text-left px-4 py-2.5 rounded-xl border transition-colors flex items-center justify-between {{ !$filterKecamatan ? 'bg-red-50 border-red-200 text-[#B9232D] font-bold' : 'bg-white border-gray-100 text-gray-700 hover:bg-gray-50 font-medium text-sm' }}">
                    Semua Kecamatan
                    @if(!$filterKecamatan)
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                    @endif
                </button>

                @foreach ($kecamatans as $kecamatan)
                    <button @click="document.getElementById('input-kecamatan').value = '{{ $kecamatan->id }}'; document.getElementById('form-filter').submit();" 
                            class="w-full text-left px-4 py-2.5 rounded-xl border transition-colors flex items-center justify-between {{ (string) $filterKecamatan === (string) $kecamatan->id ? 'bg-red-50 border-red-200 text-[#B9232D] font-bold' : 'bg-white border-gray-100 text-gray-700 hover:bg-gray-50 font-medium text-sm' }}">
                        {{ $kecamatan->nama }}
                        @if((string) $filterKecamatan === (string) $kecamatan->id)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- TOAST -->
    <div x-show="toastShow" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed top-20 left-4 right-4 z-[80] flex justify-center pointer-events-none"
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

</body>
</html>