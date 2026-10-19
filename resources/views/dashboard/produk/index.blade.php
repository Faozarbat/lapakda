<x-app-layout>
    <!-- Header -->
    <header class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-gray-100">
        <div class="max-w-3xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-700 hover:bg-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
            </a>
            <h1 class="font-bold text-base text-gray-900 absolute left-1/2 -translate-x-1/2">Kelola Produk</h1>
            <a href="{{ route('dashboard.produk.create', $store->id) }}" class="w-10 h-10 rounded-full bg-[#B9232D] flex items-center justify-center text-white shadow-md shadow-red-500/20 hover:scale-105 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
            </a>
        </div>
    </header>

    <div class="max-w-3xl mx-auto px-4 pt-6 pb-24">
        <!-- Info Toko -->
        <div class="flex items-center gap-2 mb-4">
            <div class="w-6 h-6 rounded-md bg-red-50 flex items-center justify-center text-[#B9232D] font-extrabold text-xs">
                {{ substr($store->nama, 0, 1) }}
            </div>
            <span class="font-bold text-sm text-gray-900">{{ $store->nama }}</span>
            <span class="text-xs text-gray-400 ml-auto">{{ $products->count() }} Produk</span>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-50 text-green-700 border border-green-100 rounded-xl text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                {{ session('success') }}
            </div>
        @endif

        @if ($products->isEmpty())
            <!-- Empty State -->
            <div class="text-center bg-white rounded-3xl p-10 border border-gray-100 shadow-sm mt-4">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Belum Ada Produk</h3>
                <p class="text-sm text-gray-500 mb-6">Tambahkan menu pertamamu agar bisa dipesan oleh pelanggan.</p>
                <a href="{{ route('dashboard.produk.create', $store->id) }}" class="inline-block bg-[#B9232D] text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg shadow-red-500/30 hover:scale-105 transition-transform">
                    + Tambah Produk
                </a>
            </div>
        @else
            <!-- Grid Produk -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach ($products as $product)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] overflow-hidden flex flex-col group">
                        
                        <!-- Thumbnail -->
                        <div class="aspect-square w-full bg-gray-100 relative overflow-hidden">
                            @if ($product->foto)
                                <img src="{{ asset('storage/' . $product->foto) }}" class="w-full h-full object-contain bg-gray-50 p-1 group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 opacity-40 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <span class="text-[10px]">No Foto</span>
                                </div>
                            @endif
                            
                            <!-- Badge Habis -->
                            @if (!$product->status_tersedia)
                                <div class="absolute inset-0 bg-white/70 backdrop-blur-sm flex items-center justify-center">
                                    <span class="bg-gray-800 text-white text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider">Habis</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Info -->
                        <div class="p-3 flex flex-col flex-grow">
                            <h4 class="font-bold text-sm text-gray-900 leading-tight line-clamp-2 mb-1">{{ $product->nama }}</h4>
                            <span class="font-extrabold text-sm text-[#B9232D] mt-auto">Rp{{ number_format($product->harga, 0, ',', '.') }}</span>
                        </div>

                        <!-- Aksi -->
                        <div class="flex border-t border-gray-100">
                            <a href="{{ route('dashboard.produk.edit', [$store->id, $product->id]) }}" class="flex-1 py-2.5 text-center text-xs font-bold text-gray-600 hover:bg-gray-50 transition-colors border-r border-gray-100">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('dashboard.produk.destroy', [$store->id, $product->id]) }}" class="flex-1" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full py-2.5 text-center text-xs font-bold text-red-500 hover:bg-red-50 transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>