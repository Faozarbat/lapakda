<x-app-layout>
    <div x-data="{ 
            showToast: false, 
            toastMessage: '', 
            profilPreview: null, 
            bgPreview: null,
            fileErrorProfil: '',
            fileErrorBg: '',
            voucherCode: '{{ old('voucher_code') }}'
         }"
         @show-toast.window="toastMessage = $event.detail.message; showToast = true; setTimeout(() => showToast = false, 3000)"
         class="pb-24">

        <!-- Header -->
        <header class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-gray-100">
            <div class="max-w-3xl mx-auto px-4 h-16 flex items-center justify-between">
                <!-- Tombol Batal (Back) -->
                <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-700 hover:bg-gray-200 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
                </a>
                <h1 class="font-bold text-base text-gray-900 absolute left-1/2 -translate-x-1/2">Daftar Toko Baru</h1>
                <div class="w-10"></div>
            </div>
        </header>

        <div class="max-w-3xl mx-auto px-4 pt-6">
            <form method="POST" action="{{ route('store.store') }}" enctype="multipart/form-data">
                @csrf
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <span class="font-bold text-red-800 text-sm">Gagal Menyimpan!</span>
                        </div>
                        <ul class="text-xs text-red-600 list-disc pl-5 space-y-1 font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(!$isFirstStore)
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl">
                        <h3 class="font-bold text-red-800 mb-1">Informasi Biaya</h3>
                        <p class="text-sm text-red-700">Anda sudah memiliki toko. Pembuatan toko baru dikenakan <strong>Biaya Buka sebesar Rp {{ number_format($openFee, 0, ',', '.') }}</strong> dan <strong>Langganan Rp {{ number_format($monthlyFee, 0, ',', '.') }}/bulan</strong>.</p>
                    </div>
                @endif

                <div class="space-y-5">
                    <!-- Section: Visual Toko -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm space-y-4">
                        <h3 class="font-extrabold text-gray-900 text-sm">Visual Toko (Opsional)</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Foto Profil -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">Foto Profil (Logo)</label>
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden border border-gray-200 flex-shrink-0">
                                        <template x-if="profilPreview"><img :src="profilPreview" class="w-full h-full object-cover"></template>
                                        <template x-if="!profilPreview">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </template>
                                    </div>
                                    <input type="file" name="foto_profil" accept="image/*" 
                                           @change="
                                               const file = $event.target.files[0]; 
                                               if (file) { 
                                                   if (file.size > 5 * 1024 * 1024) {
                                                       fileErrorProfil = 'Maks 5MB.';
                                                       $event.target.value = ''; 
                                                       profilPreview = null;
                                                   } else {
                                                       fileErrorProfil = '';
                                                       profilPreview = URL.createObjectURL(file);
                                                   }
                                               }
                                           " 
                                           class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-[#B9232D] hover:file:bg-red-100 transition">
                                </div>
                                <p x-show="fileErrorProfil" x-text="fileErrorProfil" class="text-red-500 text-xs font-bold mt-1" x-cloak></p>
                                @error('foto_profil') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Foto Background -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">Foto Sampul (Background)</label>
                                <div class="flex items-center gap-4">
                                    <div class="w-24 h-16 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden border border-gray-200 flex-shrink-0">
                                        <template x-if="bgPreview"><img :src="bgPreview" class="w-full h-full object-cover"></template>
                                        <template x-if="!bgPreview">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </template>
                                    </div>
                                    <input type="file" name="foto_background" accept="image/*" 
                                           @change="
                                               const file = $event.target.files[0]; 
                                               if (file) { 
                                                   if (file.size > 5 * 1024 * 1024) {
                                                       fileErrorBg = 'Maks 5MB.';
                                                       $event.target.value = ''; 
                                                       bgPreview = null;
                                                   } else {
                                                       fileErrorBg = '';
                                                       bgPreview = URL.createObjectURL(file);
                                                   }
                                               }
                                           " 
                                           class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-[#B9232D] hover:file:bg-red-100 transition">
                                </div>
                                <p x-show="fileErrorBg" x-text="fileErrorBg" class="text-red-500 text-xs font-bold mt-1" x-cloak></p>
                                @error('foto_background') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section: Info Dasar -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm space-y-4">
                        <h3 class="font-extrabold text-gray-900 text-sm">Informasi Dasar</h3>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Nama Toko/Resto</label>
                            <input type="text" name="nama" value="{{ old('nama') }}" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]">
                            @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Kategori</label>
                            <div class="flex gap-6 text-sm">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="kategori" value="resto" {{ old('kategori') === 'resto' ? 'checked' : '' }} required class="text-[#B9232D] focus:ring-[#B9232D]">
                                    Resto
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="kategori" value="store" {{ old('kategori') === 'store' ? 'checked' : '' }} class="text-[#B9232D] focus:ring-[#B9232D]">
                                    Store/Toko
                                </label>
                            </div>
                            @error('kategori') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Kecamatan</label>
                            <select name="kecamatan_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]" required>
                                <option value="">-- Pilih Kecamatan --</option>
                                @foreach ($kecamatans as $kecamatan)
                                    <option value="{{ $kecamatan->id }}" {{ old('kecamatan_id') == $kecamatan->id ? 'selected' : '' }}>{{ $kecamatan->nama }}</option>
                                @endforeach
                            </select>
                            @error('kecamatan_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Alamat Lengkap</label>
                            <input type="text" name="alamat" value="{{ old('alamat') }}" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]">
                            @error('alamat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Nomor WhatsApp Toko</label>
                            <input type="tel" name="nomor_wa" value="{{ old('nomor_wa') }}" placeholder="Contoh: 081234567890" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]">
                            <p class="text-[11px] text-gray-500 mt-1">Nomor ini yang akan dituju konsumen saat checkout.</p>
                            @error('nomor_wa') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Section: Voucher -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                        <h3 class="font-extrabold text-gray-900 text-sm mb-3">Kode Voucher (Opsional)</h3>
                        <input type="text" name="voucher_code" x-model="voucherCode" @input="voucherCode = voucherCode.toUpperCase()" placeholder="Ketik Kode Voucher Disini" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm uppercase focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]">
                        <p class="text-[11px] text-gray-500 mt-2">Gunakan kode <strong>UNDANGAN</strong> untuk gratis langganan 3 bulan pertama.</p>
                        @error('voucher_code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Section: Peta -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm space-y-3">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="font-extrabold text-gray-900 text-sm">Titik Lokasi Peta</h3>
                            <button type="button" id="btnLokasiSaya" class="text-xs font-bold text-[#B9232D] bg-red-50 px-3 py-1.5 rounded-lg hover:bg-red-100 transition-colors">
                                Gunakan Lokasi Saya
                            </button>
                        </div>
                        
                        <div id="map" class="h-64 rounded-xl border border-gray-200 z-0"></div>
                        
                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}" required>
                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}" required>
                        @error('latitude') <p class="text-red-500 text-xs mt-1">Titik lokasi wajib dipilih di peta.</p> @enderror
                    </div>

                    <!-- Tombol Submit -->
                    <div class="pt-2">
                        <button type="submit" class="w-full bg-[#B9232D] text-white py-3.5 rounded-xl text-sm font-bold shadow-lg shadow-red-500/30 hover:scale-[1.02] transition-transform">
                            Daftarkan Toko Sekarang
                        </button>
                    </div>

                </div>
            </form>
        </div>

        <!-- Toast Notification -->
        <div x-show="showToast" x-transition class="fixed top-20 left-4 right-4 z-[60] flex justify-center pointer-events-none" x-cloak>
            <div class="bg-gray-900 text-white px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 w-full max-w-sm pointer-events-auto">
                <div class="bg-red-500/20 p-1.5 rounded-lg flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                </div>
                <p class="text-sm font-medium flex-1" x-text="toastMessage"></p>
                <button @click="showToast = false" class="text-gray-400 hover:text-white"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></button>
            </div>
        </div>

        <!-- Leaflet Scripts -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
        <script>
            const defaultLat = 1.1301;
            const defaultLng = 104.0529;
            const map = L.map('map').setView([defaultLat, defaultLng], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            const marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

            function simpanTitik(latlng) {
                document.getElementById('latitude').value = latlng.lat;
                document.getElementById('longitude').value = latlng.lng;
            }

            marker.on('dragend', () => simpanTitik(marker.getLatLng()));
            map.on('click', (e) => { marker.setLatLng(e.latlng); simpanTitik(e.latlng); });

            document.getElementById('btnLokasiSaya').addEventListener('click', () => {
                if (!navigator.geolocation) {
                    window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Browser tidak mendukung deteksi lokasi. Geser pin manual saja.' } }));
                    return;
                }
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        const latlng = { lat: pos.coords.latitude, lng: pos.coords.longitude };
                        map.setView(latlng, 16);
                        marker.setLatLng(latlng);
                        simpanTitik(latlng);
                    },
                    () => {
                        window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Gagal ambil lokasi. Pastikan GPS aktif.' } }));
                    }
                );
            });

            const oldLat = document.getElementById('latitude').value;
            const oldLng = document.getElementById('longitude').value;
            if (oldLat && oldLng) {
                const latlng = { lat: parseFloat(oldLat), lng: parseFloat(oldLng) };
                map.setView(latlng, 16);
                marker.setLatLng(latlng);
            }
        </script>
    </div>
</x-app-layout>