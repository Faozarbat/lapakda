<x-app-layout>
    <!-- Header -->
    <header class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-gray-100">
        <div class="max-w-3xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('dashboard.produk.index', $store->id) }}" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-700 hover:bg-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
            </a>
            <h1 class="font-bold text-base text-gray-900 absolute left-1/2 -translate-x-1/2">Tambah Produk</h1>
            <div class="w-10"></div>
        </div>
    </header>

    <div class="max-w-3xl mx-auto px-4 pt-6 pb-24">
        <form method="POST" action="{{ route('dashboard.produk.store', $store->id) }}" enctype="multipart/form-data" class="space-y-5" x-data="{ imageUrl: null }">
            @csrf

            <!-- Upload Foto -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm text-center">
                <div class="w-40 h-40 mx-auto bg-gray-50 rounded-2xl border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden relative group hover:border-[#B9232D] transition-colors mb-3">
                    
                    <!-- Tampilan Jika Belum Ada Foto -->
                    <template x-if="!imageUrl">
                        <div class="flex flex-col items-center text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <span class="text-[10px] font-bold">Upload Foto</span>
                        </div>
                    </template>
                    
                    <!-- Tampilan Jika Foto Sudah Dipilih (FIX: pakai absolute agar pasti utuh) -->
                    <template x-if="imageUrl">
                        <img :src="imageUrl" class="absolute inset-0 w-full h-full object-contain bg-white p-1 z-0">
                    </template>

                    <!-- Input File -->
                    <input type="file" name="foto" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-10" 
                           @change="const file = $event.target.files[0]; if (file) { imageUrl = URL.createObjectURL(file) }">
                </div>
                <p class="text-[11px] text-gray-500">Format: JPG, PNG, WebP. Maks 5MB.</p>
                @error('foto') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Info Dasar -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Nama Produk</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Misal: Nasi Goreng Spesial" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]">
                    @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Stok (Opsional)</label>
                    <input type="number" name="stok" value="{{ old('stok') }}" min="0" placeholder="Kosongkan jika stok banyak" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]">
                    <p class="text-[11px] text-gray-500 mt-1">Jika diisi 0, produk otomatis berstatus "Habis".</p>
                    @error('stok') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Kategori (Opsional)</label>
                    <input type="text" name="kategori_produk" list="kategori_list" value="{{ old('kategori_produk') }}" placeholder="Pilih atau ketik kategori..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]">
                    <datalist id="kategori_list">
                        <option value="Makanan">
                        <option value="Minuman">
                        <option value="Camilan">
                    </datalist>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Deskripsi (Opsional)</label>
                    <textarea name="deskripsi" rows="3" placeholder="Jelaskan detail produk atau bahan-bahannya..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]">{{ old('deskripsi') }}</textarea>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="pt-2">
                <button type="submit" class="w-full bg-[#B9232D] text-white py-3.5 rounded-xl text-sm font-bold shadow-lg shadow-red-500/30 hover:scale-[1.02] transition-transform">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
</x-app-layout>