<x-app-layout>
    <!-- Header -->
    <header class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-gray-100">
        <div class="max-w-3xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('dashboard.produk.index', $store->id) }}" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-700 hover:bg-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
            </a>
            <h1 class="font-bold text-base text-gray-900 absolute left-1/2 -translate-x-1/2">Edit Produk</h1>
            <div class="w-10"></div>
        </div>
    </header>

    <div class="max-w-3xl mx-auto px-4 pt-6 pb-24">
        <form method="POST" action="{{ route('dashboard.produk.update', [$store->id, $product->id]) }}" enctype="multipart/form-data" class="space-y-5" 
              x-data="{ imageUrl: null, isTersedia: {{ $product->status_tersedia ? 'true' : 'false' }} }">
            @csrf
            @method('PATCH')

            <!-- Upload Foto -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm text-center">
                <div class="w-32 h-32 mx-auto bg-gray-50 rounded-2xl border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden relative group hover:border-[#B9232D] transition-colors mb-3">
                    
                    <!-- Tampilkan foto baru jika dipilih -->
                    <template x-if="imageUrl">
                        <img :src="imageUrl" class="w-full h-full object-contain bg-gray-50 p-1">
                    </template>
                    
                    <!-- Tampilkan foto lama jika tidak ada foto baru -->
                    <template x-if="!imageUrl">
                        @if($product->foto)
                            <img src="{{ asset('storage/' . $product->foto) }}" class="w-full h-full object-contain bg-gray-50 p-1 opacity-60 group-hover:opacity-40 transition-opacity">
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <span class="bg-black/50 text-white text-[10px] font-bold px-2 py-1 rounded-md">Ubah?</span>
                            </div>
                        @else
                            <div class="flex flex-col items-center text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                <span class="text-[10px] font-bold">Upload Foto</span>
                            </div>
                        @endif
                    </template>

                    <!-- Input File -->
                    <input type="file" name="foto" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer" 
                           @change="const file = $event.target.files[0]; if (file) { imageUrl = URL.createObjectURL(file) }">
                </div>
                <p class="text-[11px] text-gray-500">Biarkan kosong jika tidak ingin mengganti foto.</p>
                @error('foto') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Info Dasar -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Nama Produk</label>
                    <input type="text" name="nama" value="{{ old('nama', $product->nama) }}" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]">
                    @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Harga (Rp)</label>
                    <input type="number" name="harga" value="{{ old('harga', $product->harga) }}" required min="0" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]">
                    @error('harga') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Kategori (Opsional)</label>
                    <input type="text" name="kategori_produk" list="kategori_list" value="{{ old('kategori_produk', $product->kategori_produk) }}" placeholder="Pilih atau ketik kategori..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]">
                    <datalist id="kategori_list">
                        <option value="Makanan">
                        <option value="Minuman">
                        <option value="Camilan">
                    </datalist>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Deskripsi (Opsional)</label>
                    <textarea name="deskripsi" rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]">{{ old('deskripsi', $product->deskripsi) }}</textarea>
                </div>
            </div>

            <!-- Toggle Ketersediaan -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-bold text-gray-800">Status Ketersediaan</p>
                    <p class="text-xs text-gray-500 mt-0.5">Jika dimatikan, produk akan ditandai "Habis" dan tidak bisa dipesan.</p>
                </div>
                <button type="button" @click="isTersedia = !isTersedia" :class="isTersedia ? 'bg-[#B9232D]' : 'bg-gray-200'" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none mt-1">
                    <span aria-hidden="true" :class="isTersedia ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                </button>
                <input type="hidden" name="status_tersedia" :value="isTersedia ? 1 : 0">
            </div>
<img :src="imageUrl" class="w-full h-full object-contain bg-gray-50 p-1">
            <!-- Tombol Simpan -->
            <div class="pt-2">
                <button type="submit" class="w-full bg-[#B9232D] text-white py-3.5 rounded-xl text-sm font-bold shadow-lg shadow-red-500/30 hover:scale-[1.02] transition-transform">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>