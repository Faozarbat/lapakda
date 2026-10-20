<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#B9232D">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @include('partials.pwa-head')
    @php
        $ogFoto = $store->products->first(fn ($p) => $p->foto)?->foto;
        $cartData = \App\Models\Cart::where('session_id', session()->getId())
                    ->where('store_id', $store->id)
                    ->with('items')
                    ->first();
        $cartCount = $cartData ? (int) $cartData->items->sum('qty') : 0;
        $cartItemsMap = $cartData ? $cartData->items->pluck('qty', 'product_id')->toArray() : [];
    @endphp
    @include('partials.og-meta', [
        'ogTitle' => $store->nama . ' — LapakDA',
        'ogDescription' => ($store->kategori === 'resto' ? 'Resto' : 'Toko') . ' di ' . $store->kecamatan->nama . '. Lihat menu & pesan langsung via WhatsApp.',
        'ogImage' => $ogFoto ? asset('storage/' . $ogFoto) : asset('og-default.png'),
    ])
    
    <title>{{ $store->nama }} — LapakDA</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        [x-cloak] { display: none !important; }
        input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F9FA] text-gray-800 antialiased {{ (isset($isPreview) && $isPreview) ? 'pb-10' : 'pb-32' }}" 
      x-data="{ scrolled: false, cartCount: {{ $cartCount }}, toastShow: false, toastMessage: '', showReviews: false, activeCategory: 'Semua', showProduct: false, selectedProduct: {} }" 
      @scroll.window="scrolled = (window.pageYOffset > 20) ? true : false"
      @cart-updated.window="cartCount = $event.detail.count"
      @show-toast.window="toastMessage = $event.detail.message; toastShow = true; setTimeout(() => { toastShow = false; }, 3000)">

    <!-- BANNER PREVIEW -->
    @if(isset($isPreview) && $isPreview)
        <div class="bg-yellow-400 text-yellow-900 text-center py-2 px-4 text-xs font-bold sticky top-0 z-[60] shadow-sm flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
            Mode Pratinjau (Tampilan Pembeli)
        </div>
    @endif

    <!-- HEADER -->
    <header :class="scrolled ? 'shadow-sm bg-white/95' : 'bg-white/90'" class="backdrop-blur-md sticky {{ (isset($isPreview) && $isPreview) ? 'top-[32px]' : 'top-0' }} z-40 border-b border-gray-100 transition-shadow duration-300">
        <div class="max-w-3xl mx-auto px-4 h-16 flex items-center justify-between gap-3">
            
            <a href="{{ (isset($isPreview) && $isPreview) ? route('dashboard') : route('jelajah', $store->kategori) }}" class="w-10 h-10 rounded-full bg-gray-50 flex-shrink-0 flex items-center justify-center text-gray-700 hover:bg-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
            </a>
            
            <!-- Judul di tengah mengambil sisa ruang -->
            <h1 class="font-bold text-base text-gray-900 flex-1 text-center truncate px-2">{{ $store->nama }}</h1>
            
            <!-- Tombol Kanan -->
            @if(isset($isPreview) && $isPreview)
                <div class="w-10"></div> <!-- Spacer jika preview -->
            @else
                <div class="flex items-center gap-2 flex-shrink-0">
                    <!-- Auth Button -->
                    @auth
                        <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-[#B9232D] hover:bg-[#B9232D] hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="flex items-center justify-center px-3 h-10 rounded-full bg-[#B9232D] text-white text-xs font-bold shadow-md shadow-red-500/20">
                            Masuk
                        </a>
                    @endauth
                    
                    <a href="{{ route('order.riwayat') }}" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-700 hover:bg-gray-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    </a>

                    <button onclick="bagikanToko()" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-700 hover:bg-gray-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" /></svg>
                    </button>
                </div>
            @endif
        </div>
    </header>

    <main class="max-w-3xl mx-auto">
        
        <!-- Cover & Info Resto -->
        <div class="relative mb-6">
            <div class="h-48 w-full bg-gray-300 overflow-hidden relative group">
                @if($store->foto_background)
                    <!-- Prioritas 1: Background Toko -->
                    <img src="{{ Storage::url($store->foto_background) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" alt="Cover">
                @elseif($ogFoto)
                    <!-- Prioritas 2: Foto Produk Pertama (Fallback lama) -->
                    <img src="{{ asset('storage/' . $ogFoto) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" alt="Cover">
                @else
                    <!-- Prioritas 3: Placeholder Teks -->
                    <div class="w-full h-full bg-gradient-to-r from-gray-800 to-gray-600 flex items-center justify-center">
                        <span class="text-white/30 text-4xl font-extrabold tracking-widest uppercase">{{ $store->nama }}</span>
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
            </div>
            
            <div class="px-4 -mt-12 relative z-10">
                <div class="bg-white rounded-2xl p-5 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.1)] border border-gray-100 relative">
                    
                    <!-- FOTO PROFIL TOKO -->
                    <div class="absolute -top-10 left-6 w-20 h-20 rounded-full border-4 border-white bg-gray-50 shadow-sm overflow-hidden flex items-center justify-center z-20">
                        @if($store->foto_profil)
                            <img src="{{ Storage::url($store->foto_profil) }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-2xl font-extrabold text-[#B9232D]">{{ substr($store->nama, 0, 1) }}</span>
                        @endif
                    </div>

                    <!-- Menambahkan padding-top agar teks tidak tertutup foto profil -->
                    <div class="flex justify-between items-start mb-2 pt-8 sm:pt-4">
                        <div>
                            <h2 class="text-xl font-extrabold text-gray-900">{{ $store->nama }}</h2>
                            
                            <!-- RATING & TERJUAL -->
                            <div class="flex items-center gap-3 mt-1.5 mb-2">
                                <div class="flex items-center gap-1 bg-yellow-50 px-2 py-0.5 rounded-md border border-yellow-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                    <span class="text-xs font-bold text-gray-800">{{ $store->rating }}</span>
                                </div>
                                <button @click="showReviews = true" class="text-xs font-semibold text-gray-500 underline decoration-gray-300 underline-offset-2 hover:text-[#B9232D]">
                                    {{ $store->reviews->count() }} Ulasan
                                </button>
                                <span class="text-xs font-semibold text-gray-500">•</span>
                                <span class="text-xs font-semibold text-gray-500">{{ $store->total_terjual }} Terjual</span>
                            </div>

                            <p class="text-sm text-gray-500 mt-1 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                                {{ $store->alamat }}
                            </p>
                            <p class="text-sm text-gray-500 mt-0.5 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" /></svg>
                                {{ $store->nomor_wa }}
                            </p>
                        </div>
                        <div class="flex flex-col items-end gap-1.5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-gray-50 text-gray-700 border border-gray-200">
                                {{ $store->kategori === 'resto' ? 'Resto' : 'Store' }}
                            </span>
                            @if($store->isOpenNow())
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-green-50 text-green-700 border border-green-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Buka
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-red-50 text-red-700 border border-red-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Tutup
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Menu -->
        <div class="px-4">
            @if(!$store->isOpenNow())
                <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-4 flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                    <div>
                        <h4 class="font-bold text-red-800 text-sm">Toko Sedang Tutup / Libur</h4>
                        <p class="text-xs text-red-600 mt-1">Saat ini toko tidak menerima pesanan. Silakan kembali pada jam operasional.</p>
                    </div>
                </div>
            @endif

            @if ($store->products->isEmpty())
                <div class="text-center bg-white rounded-3xl p-10 border border-gray-100 shadow-sm mt-4">
                    <p class="text-sm text-gray-500">Toko ini belum menambahkan produk.</p>
                </div>
            @else
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-extrabold text-lg text-gray-900">
                        {{ $store->kategori === 'resto' ? 'Semua Menu' : 'Semua Produk' }}
                    </h3>
                    <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-1 rounded-md">{{ $store->products->count() }} Item</span>
                </div>

                <!-- FILTER KATEGORI -->
                @php $kategoriUnik = $store->products->pluck('kategori_produk')->filter()->unique(); @endphp
                <div class="flex overflow-x-auto gap-2 pb-3 mb-2 hide-scrollbar">
                    <button @click="activeCategory = 'Semua'" :class="activeCategory === 'Semua' ? 'bg-[#B9232D] text-white border-[#B9232D]' : 'bg-white text-gray-600 border-gray-200'" class="px-4 py-1.5 rounded-full text-xs font-bold border transition-colors whitespace-nowrap">Semua</button>
                    @foreach($kategoriUnik as $kat)
                        <button @click="activeCategory = '{{ $kat }}'" :class="activeCategory === '{{ $kat }}' ? 'bg-[#B9232D] text-white border-[#B9232D]' : 'bg-white text-gray-600 border-gray-200'" class="px-4 py-1.5 rounded-full text-xs font-bold border transition-colors whitespace-nowrap">{{ $kat }}</button>
                    @endforeach
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach ($store->products as $product)
                        @php
                            $productData = [
                                'id' => $product->id,
                                'nama' => $product->nama,
                                'harga_format' => number_format($product->harga, 0, ',', '.'),
                                'harga_raw' => $product->harga,
                                'deskripsi' => $product->deskripsi ?? 'Belum ada deskripsi untuk produk ini.',
                                'foto' => $product->foto ? asset('storage/' . $product->foto) : null,
                                'tersedia' => $product->status_tersedia ?? true
                            ];
                        @endphp
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col group cursor-pointer hover:shadow-md transition-shadow" 
                             x-show="activeCategory === 'Semua' || activeCategory === '{{ $product->kategori_produk }}'"
                             @click="selectedProduct = {{ Js::from($productData) }}; showProduct = true">
                            <div class="aspect-square w-full bg-gray-100 relative overflow-hidden">
                                @if ($product->foto)
                                    <img src="{{ asset('storage/' . $product->foto) }}" alt="{{ $product->nama }}" class="w-full h-full object-contain bg-gray-50 p-1 group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-1 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        <span class="text-[10px]">Tanpa Foto</span>
                                    </div>
                                @endif
                                
                                @if(isset($product->status_tersedia) && !$product->status_tersedia)
                                    <div class="absolute inset-0 bg-white/60 backdrop-blur-sm flex items-center justify-center">
                                        <span class="bg-gray-800 text-white text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider">Habis</span>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="p-3 flex flex-col flex-grow">
                                <h4 class="font-bold text-sm text-gray-900 leading-tight line-clamp-2 mb-1">{{ $product->nama }}</h4>
                                <span class="font-extrabold text-sm text-[#B9232D] mt-auto">Rp{{ number_format($product->harga, 0, ',', '.') }}</span>
                                @if(!is_null($product->stok))
                                    <span class="text-[10px] font-bold text-gray-500 mt-1">Sisa: {{ $product->stok }}</span>
                                @endif
                            </div>
                            
                            <!-- LOGIKA TOMBOL (PREVIEW VS TUTUP VS LIVE) -->
                            @if(isset($isPreview) && $isPreview)
                                <!-- Tombol Mati untuk Preview -->
                                <button disabled class="w-full py-2.5 bg-gray-100 text-gray-400 text-xs font-bold flex items-center justify-center gap-1 border-t border-gray-100 cursor-not-allowed">
                                    Mode Pratinjau
                                </button>
                            @elseif(!$store->isOpenNow())
                                <!-- Tombol Mati karena Toko Tutup -->
                                <button disabled class="w-full py-2.5 bg-red-50 text-red-400 text-xs font-bold flex items-center justify-center gap-1 border-t border-red-100 cursor-not-allowed">
                                    Toko Tutup
                                </button>
                            @else
                                <!-- Tombol Aktif untuk Live -->
                                <div class="w-full mt-auto border-t border-red-100 bg-red-50 text-[#B9232D]" 
                                     @click.stop
                                     x-data="{ qty: {{ $cartItemsMap[$product->id] ?? 0 }} }" 
                                     id="product-ctrl-{{ $product->id }}">
                                    
                                    <button x-show="qty === 0" 
                                            @click="qty = 1; tambahKeKeranjang({{ $product->id }}, 1, $el)"
                                            class="w-full py-2.5 text-xs font-bold hover:bg-[#B9232D] hover:text-white transition-colors flex items-center justify-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                                        Tambah
                                    </button>
                                    
                                    <div x-show="qty > 0" x-cloak class="flex items-center justify-between px-1 py-1">
                                        <button @click="qty--; tambahKeKeranjang({{ $product->id }}, qty, $el)" class="w-7 h-7 flex items-center justify-center bg-white rounded shadow-sm text-gray-600 hover:text-[#B9232D]">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" /></svg>
                                        </button>
                                        <input type="number" x-model="qty" @change="if(qty < 1) qty = 0; tambahKeKeranjang({{ $product->id }}, qty, $el)" min="0" max="99" class="w-8 text-center bg-transparent border-none outline-none text-xs font-bold text-[#B9232D] p-0 m-0">
                                        <button @click="qty++; tambahKeKeranjang({{ $product->id }}, qty, $el)" class="w-7 h-7 flex items-center justify-center bg-white rounded shadow-sm text-gray-600 hover:text-[#B9232D]">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </main>

    <!-- FLOATING CART BAR (Hanya muncul jika BUKAN Preview) -->
    @if(!isset($isPreview) || !$isPreview)
    <div class="fixed bottom-4 left-4 right-4 z-50 flex justify-center pointer-events-none">
        <div class="bg-white/95 backdrop-blur-xl border border-gray-200/60 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] p-2 w-full max-w-md pointer-events-auto flex items-center justify-between">
            <div class="flex items-center gap-3 pl-2">
                <a href="{{ route('cart.index') }}" class="relative">
                    <div class="w-10 h-10 bg-[#B9232D] rounded-full flex items-center justify-center text-white shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                    </div>
                    <div class="absolute -top-1 -right-1 min-w-[16px] h-4 bg-white rounded-full border-2 border-[#B9232D] flex items-center justify-center px-1" x-show="cartCount > 0" x-cloak>
                        <span x-text="cartCount" class="text-[9px] font-extrabold text-[#B9232D]"></span>
                    </div>
                </a>
                <div>
                    <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide">Keranjang</p>
                    <p class="text-sm font-extrabold text-gray-900">Lihat Pesanan</p>
                </div>
            </div>
            
            <a href="{{ route('cart.index') }}" class="bg-gray-100 text-gray-800 px-6 py-3 rounded-2xl text-sm font-bold shadow-sm hover:scale-105 transition-transform flex items-center gap-2">
                Buka
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
            </a>
        </div>
    </div>
    @endif

    <!-- MODAL DETAIL PRODUK (BOTTOM SHEET) -->
    <div x-show="showProduct" x-cloak class="fixed inset-0 z-[100] flex items-end justify-center sm:items-center">
        <div x-show="showProduct" x-transition.opacity class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showProduct = false"></div>
        
        <div x-show="showProduct" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-y-full sm:translate-y-8 sm:opacity-0"
             x-transition:enter-end="translate-y-0 sm:opacity-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-y-0 sm:opacity-100"
             x-transition:leave-end="translate-y-full sm:translate-y-8 sm:opacity-0"
             class="bg-white w-full max-w-lg sm:rounded-3xl rounded-t-3xl shadow-2xl relative z-10 flex flex-col max-h-[90vh]">
            
            <!-- Handle Mobile -->
            <div class="w-full flex justify-center pt-3 pb-2 sm:hidden" @click="showProduct = false">
                <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
            </div>

            <!-- Gambar Besar -->
            <div class="w-full h-64 sm:h-80 bg-gray-100 relative flex-shrink-0">
                <template x-if="selectedProduct.foto">
                    <img :src="selectedProduct.foto" class="w-full h-full object-contain bg-gray-50 p-2">
                </template>
                <template x-if="!selectedProduct.foto">
                    <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        <span class="text-xs">Tanpa Foto</span>
                    </div>
                </template>

                <!-- Tombol Close Desktop -->
                <button @click="showProduct = false" class="hidden sm:flex absolute top-4 right-4 w-8 h-8 bg-white/90 rounded-full items-center justify-center text-gray-700 shadow-md hover:bg-gray-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                </button>

                <!-- Label Habis -->
                <template x-if="!selectedProduct.tersedia">
                    <div class="absolute inset-0 bg-white/60 backdrop-blur-sm flex items-center justify-center">
                        <span class="bg-gray-800 text-white text-sm font-bold px-4 py-2 rounded-lg uppercase tracking-wider">Stok Habis</span>
                    </div>
                </template>
            </div>
            
            <!-- Info Produk -->
            <div class="p-6 overflow-y-auto flex-grow">
                <h3 class="text-xl font-extrabold text-gray-900 mb-2" x-text="selectedProduct.nama"></h3>
                <p class="text-2xl font-extrabold text-[#B9232D] mb-4">
                    Rp<span x-text="selectedProduct.harga_format"></span>
                </p>
                
                <div class="border-t border-gray-100 pt-4">
                    <h4 class="text-sm font-bold text-gray-900 mb-2">Deskripsi</h4>
                    <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line" x-text="selectedProduct.deskripsi"></p>
                </div>
            </div>

            <!-- Tombol Tambah Keranjang di Modal -->
            @if(!isset($isPreview) || !$isPreview)
                <div class="p-4 border-t border-gray-100 bg-white rounded-b-3xl sm:rounded-b-3xl shadow-[0_-4px_20px_-4px_rgba(0,0,0,0.05)]">
                    <button x-show="selectedProduct.tersedia && {{ $store->isOpenNow() ? 'true' : 'false' }}" 
                            @click="tambahKeKeranjang(selectedProduct.id, 1, $el); showProduct = false;"
                            class="w-full bg-[#B9232D] text-white py-3.5 rounded-xl text-sm font-bold shadow-lg shadow-red-500/30 hover:bg-red-800 transition-colors flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                        Tambah ke Keranjang
                    </button>
                    <button x-show="!selectedProduct.tersedia" disabled
                            class="w-full bg-gray-100 text-gray-400 py-3.5 rounded-xl text-sm font-bold cursor-not-allowed">
                        Stok Habis
                    </button>
                    <button x-show="selectedProduct.tersedia && {{ $store->isOpenNow() ? 'false' : 'true' }}" disabled
                            class="w-full bg-red-50 text-red-400 py-3.5 rounded-xl text-sm font-bold cursor-not-allowed">
                        Toko Tutup
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL ULASAN -->
    <div x-show="showReviews" x-cloak class="fixed inset-0 z-[100] flex items-end justify-center sm:items-center">
        <div x-show="showReviews" x-transition.opacity class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showReviews = false"></div>
        
        <div x-show="showReviews" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-y-full sm:translate-y-8 sm:opacity-0"
             x-transition:enter-end="translate-y-0 sm:opacity-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-y-0 sm:opacity-100"
             x-transition:leave-end="translate-y-full sm:translate-y-8 sm:opacity-0"
             class="bg-white w-full max-w-lg sm:rounded-3xl rounded-t-3xl shadow-2xl relative z-10 flex flex-col h-[80vh] sm:h-[70vh]">
            
            <div class="w-full flex justify-center pt-3 pb-2 sm:hidden" @click="showReviews = false">
                <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
            </div>

            <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white rounded-t-3xl z-20">
                <h3 class="text-lg font-extrabold text-gray-900">Ulasan Pembeli</h3>
                <button @click="showReviews = false" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                </button>
            </div>

            <div class="p-5 overflow-y-auto flex-grow space-y-4 bg-gray-50/50">
                @forelse($store->reviews as $review)
                    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-red-50 text-[#B9232D] flex items-center justify-center font-bold text-xs">
                                    {{ substr($review->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-900">{{ $review->user->name }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $review->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="flex text-yellow-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                @endfor
                            </div>
                        </div>
                        @if($review->comment)
                            <p class="text-xs text-gray-600 leading-relaxed">{{ $review->comment }}</p>
                        @else
                            <p class="text-[11px] text-gray-400 italic">Tidak ada komentar.</p>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                        <p class="text-xs text-gray-500">Belum ada ulasan untuk toko ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function bagikanToko() {
            const url = window.location.href;
            const text = "Cek menu enak di {{ $store->nama }}! Pesan langsung via LapakDA:";
            
            if (navigator.share) {
                navigator.share({ title: '{{ $store->nama }} - LapakDA', text: text, url: url })
                .catch(() => {});
            } else {
                navigator.clipboard.writeText(url).then(() => {
                    window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Link toko berhasil dicopy!' } }));
                });
            }
        }

        function tambahKeKeranjang(productId, qty, el) {
            fetch(`/cart/add/${productId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ qty: qty })
            })
            .then((res) => res.json())
            .then((data) => {
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
            })
            .catch(() => {});
        }
    </script>
</body>
</html>