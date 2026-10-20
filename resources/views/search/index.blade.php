<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#B9232D">
    
    @include('partials.pwa-head')
    <title>Pencarian — LapakDA</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F9FA] text-gray-800 antialiased pb-32">

    <!-- HEADER PENCARIAN -->
    <header class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center gap-3">
            <a href="{{ route('home') }}" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-700 hover:bg-gray-200 transition-colors flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
            </a>
            
            <!-- Search Bar Aktif -->
            <form action="{{ route('search.index') }}" method="GET" class="relative flex-grow group">
                <input type="text" name="q" value="{{ $keyword }}" autofocus
                       class="block w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D] transition-all"
                       placeholder="Cari nama resto atau makanan...">
                <button type="submit" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <svg class="h-4 w-4 text-gray-400 group-focus-within:text-[#B9232D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 pt-6">
        
        <!-- Info Pencarian -->
        <div class="mb-6">
            <h2 class="text-lg font-extrabold text-gray-900">Hasil Pencarian</h2>
            <p class="text-sm text-gray-500 mt-1">Menampilkan hasil untuk: <strong>"{{ $keyword }}"</strong></p>
        </div>

        <!-- Daftar Toko -->
        @if ($stores->isEmpty())
            <div class="text-center bg-white rounded-3xl p-10 border border-gray-100 shadow-sm mt-4">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900 mb-1">Tidak Ditemukan</h3>
                <p class="text-xs text-gray-500">Coba gunakan kata kunci lain atau periksa ejaan Anda.</p>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-8">
                @foreach ($stores as $store)
                    <!-- Card Toko (Desain sama dengan Jelajah) -->
                    <a href="{{ route('store.show', [$store->kecamatan->slug, $store->slug]) }}" class="bg-white rounded-2xl border border-gray-100 shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 group flex flex-col">
                        
                        <div class="w-full aspect-[4/3] bg-gray-100 relative overflow-hidden flex items-center justify-center">
                            @if($store->foto_profil)
                                <img src="{{ Storage::url($store->foto_profil) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $store->nama }}">
                            @elseif($store->foto_background)
                                <img src="{{ Storage::url($store->foto_background) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $store->nama }}">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-red-50 to-red-100 flex items-center justify-center">
                                    <span class="text-4xl opacity-40 group-hover:scale-110 group-hover:opacity-60 transition-all duration-500">
                                        @if($store->kategori === 'resto') 🍽️ @else 🏪 @endif
                                    </span>
                                </div>
                            @endif
                            
                            <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm px-1.5 py-0.5 rounded text-[9px] font-bold text-gray-700 flex items-center gap-1 shadow-sm">
                                <span class="w-1 h-1 rounded-full bg-green-500"></span> Buka
                            </div>
                        </div>
                        
                        <div class="p-2.5 flex-grow flex flex-col">
                            <h3 class="font-bold text-gray-900 text-sm leading-tight line-clamp-2 mb-1">{{ $store->nama }}</h3>
                            <p class="text-[10px] text-gray-500 flex items-center gap-0.5 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                                {{ $store->kecamatan->nama }}
                            </p>
                            
                            <div class="mt-auto pt-2 flex items-center justify-between border-t border-gray-50">
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Lihat Menu</span>
                                <span class="text-[#B9232D]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Paginasi -->
            <div class="flex items-center justify-center gap-2 mb-8">
                {{ $stores->links() }}
            </div>
        @endif
    </main>

    <!-- BOTTOM NAVIGATION -->
    <div class="fixed bottom-6 left-4 right-4 z-40 flex justify-center pointer-events-none">
        @include('layouts.navigation')
    </div>

</body>
</html>