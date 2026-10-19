<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftarkan Toko/Resto Baru
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('store.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Toko/Resto</label>
                        <input type="text" name="nama" value="{{ old('nama') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm" required>
                        @error('nama') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2">
                                <input type="radio" name="kategori" value="resto" {{ old('kategori') === 'resto' ? 'checked' : '' }} required>
                                Resto
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" name="kategori" value="store" {{ old('kategori') === 'store' ? 'checked' : '' }}>
                                Store
                            </label>
                        </div>
                        @error('kategori') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                        <select name="kecamatan_id" class="w-full rounded-md border-gray-300 shadow-sm" required>
                            <option value="">-- Pilih Kecamatan --</option>
                            @foreach ($kecamatans as $kecamatan)
                                <option value="{{ $kecamatan->id }}" {{ old('kecamatan_id') == $kecamatan->id ? 'selected' : '' }}>
                                    {{ $kecamatan->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('kecamatan_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                        <input type="text" name="alamat" value="{{ old('alamat') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm" required>
                        @error('alamat') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp Toko</label>
                        <input type="text" name="nomor_wa" value="{{ old('nomor_wa') }}"
                               placeholder="Contoh: 081234567890"
                               class="w-full rounded-md border-gray-300 shadow-sm" required>
                        <p class="text-xs text-gray-500 mt-1">
                            📌 Nomor ini yang akan dihubungi konsumen saat memesan — boleh beda dengan nomor akun login kamu.
                        </p>
                        @error('nomor_wa') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Titik Lokasi (wajib)</label>
                        <p class="text-xs text-gray-500 mb-2">Geser pin ke lokasi toko kamu yang sebenarnya, atau pakai tombol lokasi otomatis.</p>

                        <button type="button" id="btnLokasiSaya"
                                class="mb-2 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium hover:bg-gray-50">
                            📍 Gunakan Lokasi Saya Sekarang
                        </button>

                        <div id="map" style="height: 300px; border-radius: 8px;"></div>

                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}" required>
                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}" required>
                        @error('latitude') <p class="text-red-600 text-sm mt-1">Titik lokasi wajib dipilih di peta.</p> @enderror
                    </div>

                    <button type="submit"
                            class="w-full bg-red-600 text-white py-3 rounded-md font-semibold hover:bg-red-700">
                        Daftarkan Toko
                    </button>
                </form>

            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script>
        // Default center: Batam, dipakai kalau pemilik toko belum menggeser pin sama sekali.
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
                alert('Browser kamu tidak mendukung deteksi lokasi. Silakan geser pin manual.');
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const latlng = { lat: pos.coords.latitude, lng: pos.coords.longitude };
                    map.setView(latlng, 16);
                    marker.setLatLng(latlng);
                    simpanTitik(latlng);
                },
                () => alert('Gagal mengambil lokasi. Silakan geser pin manual di peta.')
            );
        });

        // Kalau ada nilai lama (form gagal validasi & user diarahkan balik), muat ulang posisinya
        const oldLat = document.getElementById('latitude').value;
        const oldLng = document.getElementById('longitude').value;
        if (oldLat && oldLng) {
            const latlng = { lat: parseFloat(oldLat), lng: parseFloat(oldLng) };
            map.setView(latlng, 16);
            marker.setLatLng(latlng);
        }
    </script>
</x-app-layout>