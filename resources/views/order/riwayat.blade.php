<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Pesanan — LapakDA</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } [x-cloak] { display: none !important; } </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F9FA] text-gray-800 antialiased pb-24" x-data="{ activeTab: 'pembeli' }">

    <!-- HEADER -->
    <header class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-gray-100">
        <div class="max-w-3xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-700 hover:bg-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
            </a>
            <h1 class="font-bold text-base text-gray-900 absolute left-1/2 -translate-x-1/2">Pesanan</h1>
            <a href="{{ route('home') }}" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-700 hover:bg-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" /></svg>
            </a>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 pt-6">
        
        <!-- TAB MENU (Hanya muncul jika user punya toko) -->
        @if(auth()->check() && auth()->user()->stores->isNotEmpty())
        <div class="flex bg-gray-100 p-1 rounded-2xl mb-6">
            <button @click="activeTab = 'pembeli'" :class="activeTab === 'pembeli' ? 'bg-white text-[#B9232D] shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="flex-1 py-2.5 text-sm font-bold rounded-xl transition-all">
                Pesanan Saya
            </button>
            <button @click="activeTab = 'penjual'" :class="activeTab === 'penjual' ? 'bg-white text-[#B9232D] shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="flex-1 py-2.5 text-sm font-bold rounded-xl transition-all flex items-center justify-center gap-2">
                Pesanan Masuk
                @if(isset($incomingOrders) && $incomingOrders->where('status', 'menunggu_konfirmasi')->count() > 0)
                    <span class="bg-[#B9232D] text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $incomingOrders->where('status', 'menunggu_konfirmasi')->count() }}</span>
                @endif
            </button>
        </div>
        @endif

        <!-- ========================================== -->
        <!-- TAB 1: PESANAN SAYA (PEMBELI) -->
        <!-- ========================================== -->
        <div x-show="activeTab === 'pembeli'" x-transition.opacity>
            @if($myOrders->isEmpty())
                <div class="text-center bg-white rounded-3xl p-10 border border-gray-100 shadow-sm mt-4">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Belum ada pesanan</h3>
                    <p class="text-sm text-gray-500 mb-6">Kamu belum pernah melakukan pemesanan.</p>
                    <a href="{{ route('home') }}" class="inline-block bg-[#B9232D] text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg shadow-red-500/30 hover:scale-105 transition-transform">Mulai Belanja</a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($myOrders as $order)
                        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
                            <div class="flex justify-between items-start border-b border-gray-100 pb-3 mb-3">
                                <div>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $order->created_at->format('d M Y, H:i') }}</span>
                                    <h3 class="font-extrabold text-gray-900 text-base mt-0.5">{{ $order->store->nama }}</h3>
                                </div>
                                <div class="text-right">
                                    <span class="bg-gray-100 text-gray-700 text-[9px] font-bold px-2 py-1 rounded-md uppercase">{{ str_replace('_', ' ', $order->status) }}</span>
                                    <p class="text-xs font-bold text-[#B9232D] mt-1">#{{ $order->nomor_order }}</p>
                                </div>
                            </div>
                            
                            <div class="space-y-1 mb-3">
                                @foreach($order->items as $item)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">{{ $item->qty }}x {{ $item->nama_produk_saat_beli }}</span>
                                        <span class="font-semibold text-gray-900">Rp{{ number_format($item->harga_saat_beli * $item->qty, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="flex justify-between items-center pt-3 border-t border-gray-100 mb-3">
                                <span class="text-xs font-semibold text-gray-500">Total Belanja</span>
                                <span class="text-base font-extrabold text-[#B9232D]">Rp{{ number_format($order->items->sum(fn($i) => $i->harga_saat_beli * $i->qty), 0, ',', '.') }}</span>
                            </div>
                            <a href="{{ route('chat.show', $order->id) }}" class="block w-full text-center bg-gray-50 border border-gray-200 text-gray-700 text-xs font-bold py-2.5 rounded-xl hover:bg-gray-100 transition-colors">
                                Buka Chat / Tanya Penjual
                            </a>

                            @if($order->status === 'selesai' && !$order->review)
                                <div class="mt-3 pt-3 border-t border-gray-100 flex justify-end">
                                    <button onclick="document.getElementById('review-modal-{{ $order->id }}').showModal()" class="bg-yellow-50 text-yellow-600 border border-yellow-200 px-4 py-2 rounded-xl text-xs font-bold hover:bg-yellow-100 transition-colors">
                                        Beri Ulasan
                                    </button>
                                </div>
                                <dialog id="review-modal-{{ $order->id }}" class="modal p-0 rounded-2xl shadow-xl backdrop:bg-black/50 backdrop:backdrop-blur-sm">
                                    <form method="POST" action="{{ route('review.store', $order->id) }}" class="bg-white p-6 w-[90vw] max-w-sm rounded-2xl relative">
                                        @csrf
                                        <h3 class="text-lg font-extrabold text-gray-900 mb-1 text-center">Beri Ulasan</h3>
                                        <p class="text-xs text-gray-500 text-center mb-4">Bagaimana pengalaman pesananmu di {{ $order->store->nama }}?</p>
                                        <div class="flex justify-center gap-2 mb-4">
                                            @for($i = 1; $i <= 5; $i++)
                                                <label class="cursor-pointer group">
                                                    <input type="radio" name="rating" value="{{ $i }}" class="peer hidden" required>
                                                    <svg class="h-8 w-8 text-gray-300 peer-checked:text-yellow-400 group-hover:text-yellow-300 transition-colors" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                                </label>
                                            @endfor
                                        </div>
                                        <textarea name="comment" rows="3" placeholder="Ceritakan pengalamanmu (opsional)..." class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] mb-4"></textarea>
                                        <div class="flex gap-2">
                                            <button type="button" onclick="document.getElementById('review-modal-{{ $order->id }}').close()" class="flex-1 bg-gray-100 text-gray-700 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-200">Batal</button>
                                            <button type="submit" class="flex-1 bg-[#B9232D] text-white py-2.5 rounded-xl text-sm font-bold shadow-md shadow-red-500/20 hover:bg-red-700">Kirim</button>
                                        </div>
                                    </form>
                                </dialog>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: PESANAN MASUK (PENJUAL) -->
        <!-- ========================================== -->
        @if(auth()->check() && auth()->user()->stores->isNotEmpty())
        <div x-show="activeTab === 'penjual'" x-cloak x-transition.opacity>
            @if($incomingOrders->isEmpty())
                <div class="text-center bg-white rounded-3xl p-10 border border-gray-100 shadow-sm mt-4">
                    <p class="text-sm text-gray-500">Belum ada pesanan masuk ke toko Anda.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($incomingOrders as $order)
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                            <div class="flex items-center gap-2 mb-3 pb-3 border-b border-gray-100">
                                <div class="w-6 h-6 rounded bg-red-50 text-[#B9232D] flex items-center justify-center font-bold text-xs">
                                    {{ substr($order->store->nama, 0, 1) }}
                                </div>
                                <span class="text-xs font-bold text-gray-700">{{ $order->store->nama }}</span>
                            </div>

                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="font-extrabold text-gray-900 text-base">#{{ $order->nomor_order }}</h3>
                                    <p class="text-[11px] text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-1 rounded-md border
                                    {{ $order->status === 'menunggu_konfirmasi' ? 'bg-yellow-50 text-yellow-700 border-yellow-200' : '' }}
                                    {{ $order->status === 'dikonfirmasi' ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}
                                    {{ $order->status === 'selesai' ? 'bg-green-50 text-green-700 border-green-200' : '' }}
                                    {{ $order->status === 'dibatalkan' ? 'bg-red-50 text-red-700 border-red-200' : '' }}">
                                    {{ strtoupper(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </div>

                            <div class="text-sm font-bold text-[#B9232D] mb-3">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('chat.show', $order->id) }}" class="flex-1 text-center bg-[#B9232D] text-white text-xs font-bold py-2.5 rounded-xl hover:bg-red-800 transition-colors">
                                    Chat
                                </a>
                                <a href="{{ route('dashboard.order.index', $order->store_id) }}" class="flex-1 text-center bg-gray-100 text-gray-700 text-xs font-bold py-2.5 rounded-xl hover:bg-gray-200 transition-colors">
                                    Kelola
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        @endif

    </main>

    <!-- BOTTOM NAVIGATION -->
    <div class="fixed bottom-6 left-4 right-4 z-40 flex justify-center pointer-events-none">
        @include('layouts.navigation')
    </div>

</body>
</html>