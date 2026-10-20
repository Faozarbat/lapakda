<x-app-layout>
    <div x-data="{ 
            showToast: false, 
            toastMessage: '', 
            hasTableNumber: {{ $store->has_table_number ? 'true' : 'false' }},
            statusAktif: {{ $store->status_aktif ? 'true' : 'false' }},
            profilPreview: null, 
            bgPreview: null,
            fileErrorProfil: '',
            fileErrorBg: ''
         }"
         @show-toast.window="toastMessage = $event.detail.message; showToast = true; setTimeout(() => showToast = false, 3000)"
         class="pb-24">

        <!-- Header -->
        <header class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-gray-100">
            <div class="max-w-3xl mx-auto px-4 h-16 flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-700 hover:bg-gray-200 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
                </a>
                <h1 class="font-bold text-base text-gray-900 absolute left-1/2 -translate-x-1/2">Edit Info Toko</h1>
                <div class="w-10"></div>
            </div>
        </header>

        <div class="max-w-3xl mx-auto px-4 pt-6">
            <form method="POST" action="{{ route('store.update', $store->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="space-y-5">
                    
                    <!-- Section: Visual Toko -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm space-y-4">
                        <h3 class="font-extrabold text-gray-900 text-sm">Visual Toko</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Foto Profil -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">Foto Profil (Logo)</label>
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden border border-gray-200 flex-shrink-0">
                                        <!-- Tampilkan Preview Baru Jika Dipilih, Jika Tidak Tampilkan Yang Lama -->
                                        <template x-if="profilPreview"><img :src="profilPreview" class="w-full h-full object-cover"></template>
                                        <template x-if="!profilPreview">
                                            @if($store->foto_profil)
                                                <img src="{{ Storage::url($store->foto_profil) }}" class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            @endif
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
                                            @if($store->foto_background)
                                                <img src="{{ Storage::url($store->foto_background) }}" class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            @endif
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
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Alamat Lengkap</label>
                            <input type="text" name="alamat" value="{{ old('alamat', $store->alamat) }}" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]">
                            @error('alamat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Nomor WhatsApp Toko</label>
                            <input type="tel" name="nomor_wa" value="{{ old('nomor_wa', $store->nomor_wa) }}" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]">
                            <p class="text-[11px] text-gray-500 mt-1">Nomor ini yang akan dituju saat konsumen menekan tombol checkout.</p>
                            @error('nomor_wa') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Section: Lokasi Peta -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm space-y-3">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="font-extrabold text-gray-900 text-sm">Titik Lokasi Peta</h3>
                            <button type="button" id="btnLokasiSaya" class="text-xs font-bold text-[#B9232D] bg-red-50 px-3 py-1.5 rounded-lg hover:bg-red-100 transition-colors">
                                Lokasi Saya
                            </button>
                        </div>
                        
                        <div id="map" class="h-64 rounded-xl border border-gray-200 z-0"></div>
                        
                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $store->latitude) }}" required>
                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $store->longitude) }}" required>
                    </div>

                    <!-- Section: Pengaturan Toko -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm space-y-5">
                        <h3 class="font-extrabold text-gray-900 text-sm">Pengaturan Toko</h3>
                        
                        <!-- Toggle Nomor Meja -->
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-bold text-gray-800">Gunakan Nomor Meja</p>
                                <p class="text-xs text-gray-500 mt-0.5">Jika aktif, konsumen wajib mengisi nomor meja saat memilih "Makan di Tempat".</p>
                            </div>
                            <button type="button" @click="hasTableNumber = !hasTableNumber" :class="hasTableNumber ? 'bg-[#B9232D]' : 'bg-gray-200'" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none mt-1">
                                <span aria-hidden="true" :class="hasTableNumber ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                            </button>
                            <input type="hidden" name="has_table_number" :value="hasTableNumber ? 1 : 0">
                        </div>

                        <!-- Toggle Status Aktif -->
                        <div class="flex items-start justify-between gap-4 pt-4 border-t border-gray-100">
                            <div>
                                <p class="text-sm font-bold text-gray-800">Status Toko Buka</p>
                                <p class="text-xs text-gray-500 mt-0.5">Matikan jika toko sedang tutup/libur panjang. Katalog tidak akan bisa diakses konsumen.</p>
                            </div>
                            <button type="button" @click="statusAktif = !statusAktif" :class="statusAktif ? 'bg-[#B9232D]' : 'bg-gray-200'" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none mt-1">
                                <span aria-hidden="true" :class="statusAktif ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                            </button>
                            <input type="hidden" name="status_aktif" :value="statusAktif ? 1 : 0">
                        </div>
                    </div>

                    <!-- Section: Jam Operasional -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                        <h3 class="font-extrabold text-gray-900 text-sm mb-4">Jam Operasional</h3>
                        <div class="space-y-3">
                            @foreach ($hariList as $hari)
                                @php
                                    $dataHari = $jamOperasional[$hari] ?? null;
                                    $isLibur = $dataHari['libur'] ?? false;
                                    $jamBuka = $dataHari['buka'] ?? '08:00';
                                    $jamTutup = $dataHari['tutup'] ?? '22:00';
                                @endphp
                                <div class="flex items-center gap-2 sm:gap-3 text-sm border-b border-gray-50 pb-3 last:border-0 last:pb-0">
                                    <span class="w-16 capitalize font-bold text-gray-700 text-xs">{{ $hari }}</span>

                                    <label class="flex items-center gap-1.5 text-xs text-gray-500 cursor-pointer w-14">
                                        <input type="checkbox" name="jam[{{ $hari }}][libur]" value="1" class="libur-checkbox rounded text-[#B9232D] focus:ring-[#B9232D] border-gray-300" data-hari="{{ $hari }}" {{ $isLibur ? 'checked' : '' }}>
                                        Libur
                                    </label>

                                    <div class="flex items-center gap-1 flex-grow">
                                        <input type="time" name="jam[{{ $hari }}][buka]" value="{{ $jamBuka }}" class="jam-input-{{ $hari }} w-full bg-gray-50 border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-gray-700 focus:outline-none focus:border-[#B9232D] disabled:opacity-50" {{ $isLibur ? 'disabled' : '' }}>
                                        <span class="text-gray-400">-</span>
                                        <input type="time" name="jam[{{ $hari }}][tutup]" value="{{ $jamTutup }}" class="jam-input-{{ $hari }} w-full bg-gray-50 border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-gray-700 focus:outline-none focus:border-[#B9232D] disabled:opacity-50" {{ $isLibur ? 'disabled' : '' }}>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Tombol Simpan -->
                    <div class="pt-2">
                        <button type="submit" class="w-full bg-[#B9232D] text-white py-3.5 rounded-xl text-sm font-bold shadow-lg shadow-red-500/30 hover:scale-[1.02] transition-transform">
                            Simpan Perubahan
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
            const startLat = {{ $store->latitude }};
            const startLng = {{ $store->longitude }};

            const map = L.map('map').setView([startLat, startLng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            const marker = L.marker([startLat, startLng], { draggable: true }).addTo(map);

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
                        window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Gagal mengambil lokasi. Pastikan izin GPS aktif atau geser pin manual.' } }));
                    }
                );
            });

            // Logika Toggle Libur
            document.querySelectorAll('.libur-checkbox').forEach((checkbox) => {
                checkbox.addEventListener('change', function () {
                    const hari = this.dataset.hari;
                    document.querySelectorAll(`.jam-input-${hari}`).forEach((input) => {
                        input.disabled = this.checked;
                    });
                });
            });
        </script>
    </div>
</x-app-layout>