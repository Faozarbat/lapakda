<x-app-layout>
    <!-- Header -->
    <header class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-gray-100">
        <div class="max-w-3xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-700 hover:bg-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
            </a>
            <h1 class="font-bold text-base text-gray-900 absolute left-1/2 -translate-x-1/2">Pesanan Masuk</h1>
            <div class="w-10"></div>
        </div>
    </header>

    <div class="max-w-3xl mx-auto px-4 pt-6 pb-24">
        
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-50 text-green-700 border border-green-100 rounded-xl text-sm font-medium flex items-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- FILTER TABS -->
        <div class="flex overflow-x-auto gap-2 pb-3 mb-2 hide-scrollbar">
            @php
                $tabs = [
                    'semua' => ['label' => 'Semua', 'count' => $totalSemua, 'color' => 'bg-gray-900 text-white border-gray-900'],
                    'menunggu_konfirmasi' => ['label' => 'Menunggu', 'count' => $counts['menunggu_konfirmasi'] ?? 0, 'color' => 'bg-yellow-500 text-white border-yellow-500'],
                    'dikonfirmasi' => ['label' => 'Diproses', 'count' => $counts['dikonfirmasi'] ?? 0, 'color' => 'bg-blue-600 text-white border-blue-600'],
                    'selesai' => ['label' => 'Selesai', 'count' => $counts['selesai'] ?? 0, 'color' => 'bg-green-600 text-white border-green-600'],
                    'dibatalkan' => ['label' => 'Batal', 'count' => $counts['dibatalkan'] ?? 0, 'color' => 'bg-red-600 text-white border-red-600'],
                ];
            @endphp

            @foreach($tabs as $key => $tab)
                <a href="{{ request()->fullUrlWithQuery(['status' => $key, 'page' => 1]) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold border transition-colors whitespace-nowrap flex items-center gap-1.5 {{ $statusFilter === $key ? $tab['color'] : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                    {{ $tab['label'] }}
                    @if($tab['count'] > 0)
                        <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $statusFilter === $key ? 'bg-white/20' : 'bg-gray-100 text-gray-500' }}">{{ $tab['count'] }}</span>
                    @endif
                </a>
            @endforeach
        </div>

        @if ($orders->isEmpty())
            <!-- Empty State -->
            <div class="text-center bg-white rounded-3xl p-10 border border-gray-100 shadow-sm mt-4">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Tidak Ada Pesanan</h3>
                <p class="text-sm text-gray-500">Belum ada pesanan untuk filter ini.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($orders as $order)
                    @php
                        $warnaBadge = match($order->status) {
                            'menunggu_konfirmasi' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                            'dikonfirmasi' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'selesai' => 'bg-green-50 text-green-700 border-green-200',
                            'dibatalkan' => 'bg-red-50 text-red-700 border-red-200',
                        };
                        $labelStatus = match($order->status) {
                            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                            'dikonfirmasi' => 'Diproses',
                            'selesai' => 'Selesai',
                            'dibatalkan' => 'Dibatalkan',
                        };
                    @endphp

                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <!-- Header Card: No Order & Status -->
                        <div class="p-4 border-b border-gray-100 flex justify-between items-start">
                            <div>
                                <h3 class="font-extrabold text-gray-900 text-base">#{{ $order->nomor_order }}</h3>
                                <p class="text-[11px] text-gray-500 font-medium mt-0.5">{{ $order->created_at->format('d M Y, H:i') }} WIB</p>
                            </div>
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-md border {{ $warnaBadge }}">
                                {{ $labelStatus }}
                            </span>
                        </div>

                        <div class="p-4">
                            <!-- Info Pembeli -->
                            <div class="mb-3 flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-[10px]">
                                    {{ substr($order->user->name ?? 'T', 0, 1) }}
                                </div>
                                <span class="text-xs font-bold text-gray-700">{{ $order->user->name ?? 'Tamu (Guest)' }}</span>
                                @if($order->user && $order->user->nomor_hp)
                                    <a href="https://wa.me/{{ $order->user->nomor_hp }}" target="_blank" class="text-[10px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded border border-green-200">Chat WA</a>
                                @endif
                            </div>

                            <!-- Info Fulfillment -->
                            <div class="mb-3 flex flex-wrap gap-2">
                                @if ($order->fulfillment_type === 'self_pickup')
                                    <span class="inline-flex items-center gap-1 bg-gray-50 text-gray-700 border border-gray-200 text-[11px] font-bold px-2 py-1 rounded-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                                        Ambil Sendiri
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 border border-blue-200 text-[11px] font-bold px-2 py-1 rounded-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7h-3v5h3.05a2.5 2.5 0 014.9 0H19a1 1 0 001-1v-2.586a1 1 0 00-.293-.707l-4.414-4.414A1 1 0 0014.586 3H14v4z" /></svg>
                                        Diantar
                                    </span>
                                    <a href="https://www.google.com/maps?q={{ $order->delivery_latitude }},{{ $order->delivery_longitude }}" target="_blank" class="inline-flex items-center gap-1 bg-white text-[#B9232D] border border-red-200 hover:bg-red-50 text-[11px] font-bold px-2 py-1 rounded-md transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                                        Peta
                                    </a>
                                @endif
                            </div>

                            @if ($order->fulfillment_type === 'delivery' && $order->delivery_note)
                                <div class="bg-orange-50 text-orange-800 text-[11px] p-2 rounded-lg mb-3 border border-orange-100">
                                    <span class="font-bold">Catatan Alamat:</span> {{ $order->delivery_note }}
                                </div>
                            @endif

                            <!-- List Item -->
                            <div class="bg-gray-50 rounded-xl p-3 mb-3 space-y-2 border border-gray-100">
                                @foreach ($order->items as $item)
                                    <div class="flex justify-between items-start text-sm border-b border-gray-200/60 pb-2 last:border-0 last:pb-0">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-gray-800 text-xs">{{ $item->nama_produk_saat_beli }} <span class="text-[#B9232D]">x{{ $item->qty }}</span></span>
                                            
                                            @if ($store->kategori === 'resto')
                                                @if ($item->consumption_type === 'dine_in')
                                                    <span class="text-[9px] font-bold text-green-600 bg-green-100 px-1.5 py-0.5 rounded mt-1 inline-block w-max">
                                                        Makan di tempat {{ $item->table_number ? '— Meja '.$item->table_number : '' }}
                                                    </span>
                                                @elseif ($item->consumption_type === 'takeaway')
                                                    <span class="text-[9px] font-medium text-gray-500 bg-gray-200 px-1.5 py-0.5 rounded mt-1 inline-block w-max">
                                                        Bungkus
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                        <span class="font-bold text-gray-800 text-xs">Rp{{ number_format($item->harga_saat_beli * $item->qty, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Total -->
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-xs font-bold text-gray-500">Total Belanja</span>
                                <span class="text-lg font-extrabold text-[#B9232D]">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>

                            <!-- ACTION BUTTONS (Berdasarkan Status) -->
                            <div class="flex gap-2">
                                @if($order->status === 'menunggu_konfirmasi')
                                    <form method="POST" action="{{ route('dashboard.order.updateStatus', [$store->id, $order->id]) }}" class="flex-1">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="dibatalkan">
                                        <button type="submit" class="w-full bg-gray-100 text-gray-700 text-xs font-bold py-2.5 rounded-xl hover:bg-gray-200 transition-colors">Tolak</button>
                                    </form>
                                    <form method="POST" action="{{ route('dashboard.order.updateStatus', [$store->id, $order->id]) }}" class="flex-1">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="dikonfirmasi">
                                        <button type="submit" class="w-full bg-blue-600 text-white text-xs font-bold py-2.5 rounded-xl hover:bg-blue-700 transition-colors shadow-md shadow-blue-500/20">Terima Pesanan</button>
                                    </form>
                                @elseif($order->status === 'dikonfirmasi')
                                    <form method="POST" action="{{ route('dashboard.order.updateStatus', [$store->id, $order->id]) }}" class="flex-1">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="selesai">
                                        <button type="submit" class="w-full bg-[#B9232D] text-white text-xs font-bold py-2.5 rounded-xl hover:bg-red-800 transition-colors shadow-md shadow-red-500/20">Tandai Selesai</button>
                                    </form>
                                @else
                                    <div class="w-full bg-gray-50 text-gray-400 border border-gray-100 text-xs font-bold py-2.5 rounded-xl text-center">
                                        Pesanan Telah {{ $order->status === 'selesai' ? 'Selesai' : 'Dibatalkan' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginasi -->
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</x-app-layout>