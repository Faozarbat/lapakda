<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Keranjang Belanja — LapakDA</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F9FA] text-gray-800 antialiased pb-32">

    <!-- HEADER -->
    <header class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-gray-100">
        <div class="max-w-3xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-700 hover:bg-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
            </a>
            <h1 class="font-bold text-base text-gray-900 absolute left-1/2 -translate-x-1/2">Keranjang</h1>
            <a href="{{ route('order.riwayat') }}" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-700 hover:bg-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
            </a>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 pt-4">
        
        @if ($carts->isEmpty())
            <!-- Empty State -->
            <div class="text-center bg-white rounded-3xl p-10 border border-gray-100 shadow-sm mt-8">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Keranjang Kosong</h3>
                <p class="text-sm text-gray-500 mb-6">Kamu belum menambahkan menu apapun.</p>
                <a href="{{ route('home') }}" class="inline-block bg-[#B9232D] text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg shadow-red-500/30 hover:scale-105 transition-transform">
                    Mulai Belanja
                </a>
            </div>
        @else
            <!-- Looping Per Toko -->
            <div class="space-y-6">
                @foreach ($carts as $cart)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        
                        <!-- Header Toko -->
                        <div class="bg-gray-50/80 px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                            <div class="w-6 h-6 rounded-md bg-[#B9232D] flex items-center justify-center text-white font-extrabold text-xs">
                                {{ substr($cart->store->nama, 0, 1) }}
                            </div>
                            <span class="font-bold text-sm text-gray-900">{{ $cart->store->nama }}</span>
                            <span class="text-[10px] bg-gray-200 text-gray-600 px-2 py-0.5 rounded-md ml-auto">{{ $cart->store->kecamatan->nama }}</span>
                        </div>

                        <!-- List Item -->
                        <div class="p-4 space-y-4">
                            @foreach ($cart->items as $item)
                                <div class="flex gap-3" id="item-container-{{ $item->id }}">
                                    <div class="w-14 h-14 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0">
                                        @if($item->product->foto)
                                            <img src="{{ asset('storage/' . $item->product->foto) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex flex-col justify-between flex-grow">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-bold text-gray-900 text-sm leading-tight">{{ $item->product->nama }}</h4>
                                                <span class="font-bold text-xs text-[#B9232D] mt-1 block">Rp{{ number_format($item->product->harga, 0, ',', '.') }}</span>
                                            </div>
                                            <button onclick="hapusItem({{ $item->id }})" class="text-gray-400 hover:text-red-500 transition-colors p-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                            </button>
                                        </div>
                                        
                                        <!-- Stepper Qty -->
                                        <div class="flex items-center justify-end mt-2">
                                            <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden bg-gray-50">
                                                <button onclick="ubahQty({{ $item->id }}, {{ $item->qty }} - 1)" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-200 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" /></svg>
                                                </button>
                                                <input type="number" value="{{ $item->qty }}" min="1" max="99" onchange="ubahQty({{ $item->id }}, this.value)" class="w-10 text-center text-sm font-bold text-gray-900 bg-white h-8 border-x border-gray-200 focus:outline-none p-0 m-0">
                                                <button onclick="ubahQty({{ $item->id }}, {{ $item->qty }} + 1)" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-200 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Footer (Total & Checkout) -->
                        <div class="bg-gray-50 p-4 border-t border-gray-100 flex items-center justify-between">
                            <div>
                                <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide">Total Toko Ini</p>
                                <p class="text-base font-extrabold text-gray-900">Rp{{ number_format($cart->items->sum(fn($i) => $i->product->harga * $i->qty), 0, ',', '.') }}</p>
                            </div>
                            <a href="{{ route('checkout.show', $cart->store->id) }}" class="bg-[#B9232D] text-white px-5 py-2.5 rounded-xl text-xs font-bold shadow-md shadow-red-500/20 hover:scale-105 transition-transform flex items-center gap-2">
                                Checkout
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </main>

    @include('layouts.navigation')

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function ubahQty(itemId, qtyBaru) {
            if (qtyBaru < 1) { hapusItem(itemId); return; }

            fetch(`/cart/item/${itemId}`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ qty: qtyBaru }),
            }).then(() => location.reload());
        }

        function hapusItem(itemId) {
            fetch(`/cart/item/${itemId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken },
            }).then(() => location.reload());
        }
    </script>
</body>
</html>