<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#B9232D">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout — {{ $store->nama }}</title>
    
    @include('partials.pwa-head')
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        
        /* Styling Peta */
        #map { height: 200px; border-radius: 1rem; z-index: 10; display: none; margin-top: 1rem; border: 1px solid #e5e7eb; }
        .leaflet-container { font-family: inherit; }
        
        /* Menyembunyikan panah spinner input number */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F9FA] text-gray-800 antialiased" 
      x-data="{ 
          fulfillment: '{{ old('fulfillment_type', 'self_pickup') }}',
          hasTableNumber: {{ $store->has_table_number ? 'true' : 'false' }},
          dineInQty: {{ json_encode($cart->items->values()->mapWithKeys(fn($item, $key) => [$key => $item->qty])) }},
          isLocating: false,
          toastShow: false,
          toastMessage: '',
          
          get hasDineIn() {
              return Object.values(this.dineInQty).some(qty => qty > 0);
          },
          updateQty(index, delta, max) {
              let newVal = this.dineInQty[index] + delta;
              if (newVal >= 0 && newVal <= max) {
                  this.dineInQty[index] = newVal;
              }
          }
      }"
      @show-toast.window="toastMessage = $event.detail.message; toastShow = true; setTimeout(() => { toastShow = false; }, 4000)">

    <!-- HEADER -->
    <header class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-gray-100">
        <div class="max-w-3xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('cart.show', $store->id) }}" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-700 hover:bg-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
            </a>
            <h1 class="font-bold text-base text-gray-900 absolute left-1/2 -translate-x-1/2">Checkout</h1>
            <div class="w-10"></div>
        </div>
    </header>

    <form method="POST" action="{{ route('checkout.store', $store->id) }}" id="checkoutForm">
        @csrf
        <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}">
        <input type="hidden" name="fulfillment_type" x-model="fulfillment">

        <!-- Ditambahkan pb-40 (padding bottom) yang sangat lega agar konten bisa discroll melewati tombol fixed -->
        <main class="max-w-3xl mx-auto px-4 pt-6 pb-40 space-y-4">

            <!-- Pilihan Fulfillment -->
            <div class="bg-white rounded-2xl border border-gray-100 p-1.5 flex gap-1 shadow-sm">
                <button type="button" @click="fulfillment = 'self_pickup'; window.dispatchEvent(new CustomEvent('init-map'))" 
                        :class="{'bg-[#B9232D] text-white shadow-md': fulfillment === 'self_pickup', 'text-gray-500 hover:bg-gray-50': fulfillment !== 'self_pickup'}" 
                        class="flex-1 py-3 rounded-xl text-sm font-bold transition-all flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 2a4 4 0 100 8 4 4 0 000-8zM4 18a6 6 0 1112 0H4z" clip-rule="evenodd" /></svg>
                    Ambil Sendiri
                </button>
                <button type="button" @click="fulfillment = 'delivery'; window.dispatchEvent(new CustomEvent('init-map'))" 
                        :class="{'bg-[#B9232D] text-white shadow-md': fulfillment === 'delivery', 'text-gray-500 hover:bg-gray-50': fulfillment !== 'delivery'}" 
                        class="flex-1 py-3 rounded-xl text-sm font-bold transition-all flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" /><path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7h-3v5h3.05a2.5 2.5 0 014.9 0H19a1 1 0 001-1v-2.586a1 1 0 00-.293-.707l-4.414-4.414A1 1 0 0014.586 3H14v4z" /></svg>
                    Di Antar
                </button>
            </div>
            @error('fulfillment_type') <div class="text-red-500 text-xs font-bold px-2">{{ $message }}</div> @enderror

            <!-- Self Pickup Section -->
            <div x-show="fulfillment === 'self_pickup'" x-transition.opacity class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm space-y-4">
                @if($store->kategori === 'resto')
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-1.5 h-4 bg-[#B9232D] rounded-full"></div>
                        <h3 class="font-bold text-sm text-gray-900">Rincian Konsumsi</h3>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">Kamu bisa memecah pesanan: sebagian dimakan di tempat, sebagian dibungkus.</p>
                @else
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-1.5 h-4 bg-[#B9232D] rounded-full"></div>
                        <h3 class="font-bold text-sm text-gray-900">Rincian Pesanan</h3>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">Pesanan akan disiapkan untuk diambil sendiri (Takeaway).</p>
                @endif

                @foreach ($cart->items->values() as $index => $item)
                    <div class="border border-gray-100 rounded-xl p-4 bg-gray-50/50">
                        <div class="flex justify-between items-start mb-3">
                            <h4 class="font-bold text-sm text-gray-900">{{ $item->product->nama }}</h4>
                            <span class="text-xs font-bold text-gray-500 bg-gray-200 px-2 py-0.5 rounded-md">Total: {{ $item->qty }}</span>
                        </div>
                        
                        <div class="flex flex-col gap-2">
                            @if($store->kategori === 'resto')
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-semibold text-gray-700">Makan di Tempat</label>
                                <div class="flex items-center border border-gray-200 rounded-lg bg-white overflow-hidden shadow-sm">
                                    <button type="button" @click="updateQty({{ $index }}, -1, {{ $item->qty }})" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" /></svg>
                                    </button>
                                    
                                    <input 
                                        type="number" 
                                        x-model.number="dineInQty[{{ $index }}]" 
                                        @change="
                                            let val = parseInt($event.target.value) || 0;
                                            if (val < 0) val = 0;
                                            if (val > {{ $item->qty }}) val = {{ $item->qty }};
                                            dineInQty[{{ $index }}] = val;
                                        "
                                        min="0" 
                                        max="{{ $item->qty }}" 
                                        class="w-10 text-center text-sm font-bold text-gray-900 border-x border-gray-100 bg-transparent focus:outline-none focus:ring-0 p-0 m-0"
                                    >
                                    
                                    <button type="button" @click="updateQty({{ $index }}, 1, {{ $item->qty }})" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                @if($store->kategori === 'resto')
                                    <span class="text-xs font-semibold text-gray-500">Bungkus (Takeaway)</span>
                                    <span x-text="{{ $item->qty }} - dineInQty[{{ $index }}]" class="text-sm font-bold text-[#B9232D] bg-red-50 px-2 py-0.5 rounded-md"></span>
                                @else
                                    <span class="text-xs font-semibold text-gray-500">Total Item</span>
                                    <span class="text-sm font-bold text-[#B9232D] bg-red-50 px-2 py-0.5 rounded-md">{{ $item->qty }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <input type="hidden" name="dine_in_qty[{{ $index }}]" x-model="dineInQty[{{ $index }}]" data-max="{{ $item->qty }}">
                    </div>
                @endforeach

                <!-- Nomor Meja -->
                <template x-if="hasTableNumber && hasDineIn">
                    <div class="pt-2">
                        <label for="table_number" class="block text-xs font-bold text-gray-700 mb-1.5">Nomor Meja <span class="text-red-500">*</span></label>
                        <input type="text" id="table_number" name="table_number" value="{{ old('table_number') }}" placeholder="Misal: A3 atau VIP-2" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]">
                        @error('table_number') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                </template>
            </div>

            <!-- Delivery Section -->
            <div x-show="fulfillment === 'delivery'" x-transition.opacity x-cloak class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm space-y-4">
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-4 bg-[#B9232D] rounded-full"></div>
                    <h3 class="font-bold text-sm text-gray-900">Lokasi Pengantaran</h3>
                </div>
                
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" id="btnLokasiSaya" class="flex items-center justify-center gap-1.5 bg-white border border-gray-200 text-gray-700 py-2.5 rounded-xl text-xs font-bold hover:bg-gray-50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                        Lokasi Saya
                    </button>
                    <button type="button" id="btnPilihPeta" class="flex items-center justify-center gap-1.5 bg-white border border-gray-200 text-gray-700 py-2.5 rounded-xl text-xs font-bold hover:bg-gray-50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V3zm9 3a2 2 0 11-4 0 2 2 0 014 0zm-2 6a4 4 0 00-4 4v1h8v-1a4 4 0 00-4-4z" clip-rule="evenodd" /></svg>
                        Pilih di Peta
                    </button>
                </div>

                <input type="hidden" name="delivery_latitude" id="delivery_latitude" value="{{ old('delivery_latitude', $savedAddress->last_latitude ?? '') }}">
                <input type="hidden" name="delivery_longitude" id="delivery_longitude" value="{{ old('delivery_longitude', $savedAddress->last_longitude ?? '') }}">                
                @error('delivery_latitude') 
                    <div class="text-red-500 text-xs font-bold mb-2">{{ $message }}</div> 
                @enderror

                <!-- Posisi Baru: Catatan Alamat Ditaruh di Atas Peta -->
                <div>
                    <label for="delivery_note" class="block text-xs font-bold text-gray-700 mb-1.5">Catatan Alamat (Opsional)</label>
                        <textarea id="delivery_note" name="delivery_note" rows="2" placeholder="Misal: Pagar hijau, rumah nomor 12" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]">{{ old('delivery_note', $savedAddress->last_delivery_note ?? '') }}</textarea>                </div>

                <!-- Peta Leaflet di Posisi Bawah -->
                <div id="map"></div>
            </div>

        </main>

        <!-- Tombol Submit Fixed di Bawah -->
        <div class="fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-xl border-t border-gray-200/60 p-4 pb-[calc(1rem+env(safe-area-inset-bottom))]">
            <div class="max-w-3xl mx-auto">
                <button type="submit" id="submitBtn" class="w-full bg-[#25D366] text-white py-3.5 rounded-xl text-sm font-bold shadow-lg shadow-green-500/30 hover:scale-[1.02] transition-transform flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    Kirim Pesanan via WhatsApp
                </button>
            </div>
        </div>
    </form>

    <!-- Toast Notification -->
    <div x-show="toastShow" x-transition class="fixed top-20 left-4 right-4 z-[60] flex justify-center pointer-events-none" x-cloak>
        <div class="bg-gray-900 text-white px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 w-full max-w-sm pointer-events-auto">
            <div class="bg-red-500/20 p-1.5 rounded-lg flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
            </div>
            <p class="text-sm font-medium flex-1" x-text="toastMessage"></p>
            <button @click="toastShow = false" class="text-gray-400 hover:text-white"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></button>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script>
        const storeLat = {{ $store->latitude }};
        const storeLng = {{ $store->longitude }};
        let map, marker;
        let mapInitialized = false;

        function initMap() {
            if (mapInitialized) return; 
            const mapDiv = document.getElementById('map');
            if (mapDiv.style.display === 'none') return;

            map = L.map('map').setView([storeLat, storeLng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            
            marker = L.marker([storeLat, storeLng], { draggable: true }).addTo(map);
            marker.on('dragend', simpanTitikDariMarker);
            map.on('click', (e) => { marker.setLatLng(e.latlng); simpanTitikDariMarker(); });
            
            const oldLat = document.getElementById('delivery_latitude').value;
            const oldLng = document.getElementById('delivery_longitude').value;
            if (oldLat && oldLng) {
                const pos = [parseFloat(oldLat), parseFloat(oldLng)];
                map.setView(pos, 16);
                marker.setLatLng(pos);
            } else {
                simpanTitikDariMarker(); 
            }

            setTimeout(() => map.invalidateSize(), 200); 
            mapInitialized = true;
        }

        function simpanTitikDariMarker() {
            if (!marker) return;
            const pos = marker.getLatLng();
            document.getElementById('delivery_latitude').value = pos.lat;
            document.getElementById('delivery_longitude').value = pos.lng;
        }

        window.addEventListener('init-map', () => {
            const mapDiv = document.getElementById('map');
            mapDiv.style.display = 'block'; 
            setTimeout(() => { initMap(); }, 300); 
        });

        document.getElementById('btnPilihPeta').addEventListener('click', () => {
            document.getElementById('map').style.display = 'block';
            setTimeout(() => { initMap(); }, 100);
        });

        document.getElementById('btnLokasiSaya').addEventListener('click', async () => {
            if (!navigator.geolocation) {
                window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Browser kamu tidak mendukung deteksi lokasi.' } }));
                return;
            }

            const btn = document.getElementById('btnLokasiSaya');
            btn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Mencari...';
            
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    document.getElementById('map').style.display = 'block';
                    setTimeout(() => { 
                        initMap(); 
                        const latlng = [pos.coords.latitude, pos.coords.longitude];
                        map.setView(latlng, 16);
                        marker.setLatLng(latlng);
                        simpanTitikDariMarker();
                    }, 100);
                    
                    btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg> Lokasi Ditemukan';
                },
                () => {
                    window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Gagal mengambil lokasi. Pastikan izin GPS (HTTPS) diaktifkan.' } }));
                    btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg> Lokasi Saya';
                }
            );
        });

        document.getElementById('checkoutForm').addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...';
        });
    </script>

            <!-- GUEST POPUP -->
    <div x-data="{ 
             showGuestPopup: false,
             isSubmitting: false,
             guestName: '',
             guestPhone: '',
             init() {
                 // Cek kapan terakhir kali popup ini ditutup/di-submit
                 const promptedUntil = localStorage.getItem('lapakda_guest_prompted_until');
                 
                 // Jika belum pernah (null) ATAU waktu sekarang sudah melewati batas cooldown
                 if (!promptedUntil || Date.now() > parseInt(promptedUntil)) {
                     setTimeout(() => { this.showGuestPopup = true }, 1500);
                 }
             },
             closePopup() {
                 this.showGuestPopup = false;
                 // Set cooldown selama 2 Jam (2 * 60 * 60 * 1000 milidetik)
                 const nextPrompt = Date.now() + (2 * 60 * 60 * 1000);
                 localStorage.setItem('lapakda_guest_prompted_until', nextPrompt);
             },
             submitGuestForm() {
                 if(this.isSubmitting) return;
                 this.isSubmitting = true;

                 // Ambil CSRF Token dari meta tag
                 const csrfToken = document.querySelector('meta[name=csrf-token]')?.content || '{{ csrf_token() }}';

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
                         // Hapus cooldown agar jika dia logout nanti, popup bisa muncul lagi
                         localStorage.removeItem('lapakda_guest_prompted_until');
                         // Reload halaman agar status login di header berubah otomatis
                         window.location.reload(); 
                     }
                 })
                 .catch(err => {
                     console.error(err);
                     this.isSubmitting = false;
                     alert('Terjadi kesalahan sistem, silakan coba lagi.'); // Fallback (kita hindari alert jika mungkin, tapi ini untuk darurat)
                 });
             }
         }" 
         x-show="showGuestPopup" 
         x-cloak 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        
        <!-- Backdrop Blur -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closePopup()"></div>

        <!-- Modal Box -->
        <div class="bg-white rounded-3xl w-full max-w-sm relative z-10 p-6 shadow-2xl"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95">
            
            <!-- Tombol X -->
            <button @click="closePopup()" class="absolute top-4 right-4 w-8 h-8 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 hover:bg-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
            </button>

            <!-- Ikon Sapaan -->
            <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                👋
            </div>

            <h3 class="text-xl font-extrabold text-gray-900 text-center mb-2">Kenalan Dulu Yuk!</h3>
            <p class="text-sm text-gray-500 text-center mb-6 leading-relaxed">
                Isi nama dan nomor WA kamu biar pesanan otomatis tersimpan di Riwayat dan kamu dapat update promo menarik dari resto favoritmu.
            </p>

            <form @submit.prevent="submitGuestForm()" class="space-y-3">
                <div>
                    <input type="text" x-model="guestName" placeholder="Nama Lengkap" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]" required>
                </div>
                <div>
                    <input type="tel" x-model="guestPhone" placeholder="Nomor WhatsApp (cth: 0812...)" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]" required>
                </div>
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
</body>
</html>