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
            <div class="mb-4 p-3 bg-green-50 text-green-700 border border-green-100 rounded-xl text-sm font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                {{ session('success') }}
            </div>
        @endif

        @if ($orders->isEmpty())
            <!-- Empty State -->
            <div class="text-center bg-white rounded-3xl p-10 border border-gray-100 shadow-sm mt-4">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Belum Ada Pesanan</h3>
                <p class="text-sm text-gray-500">Pesanan dari pelanggan akan muncul di sini.</p>
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
                            'dikonfirmasi' => 'Dikonfirmasi',
                            'selesai' => 'Selesai',
                            'dibatalkan' => 'Dibatalkan',
                        };
                    @endphp

                    <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
                        <!-- Header Card: No Order & Status -->
                        <div class="flex justify-between items-start border-b border-gray-100 pb-3 mb-3">
                            <div>
                                <h3 class="font-extrabold text-gray-900 text-base">#{{ $order->nomor_order }}</h3>
                                <p class="text-[11px] text-gray-500 font-medium mt-0.5">{{ $order->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-md border {{ $warnaBadge }}">
                                {{ $labelStatus }}
                            </span>
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
                                    Buka Peta
                                </a>
                            @endif
                        </div>

                        @if ($order->fulfillment_type === 'delivery' && $order->delivery_note)
                            <div class="bg-orange-50 text-orange-800 text-[11px] p-2 rounded-lg mb-3 border border-orange-100">
                                <span class="font-bold">Catatan Alamat:</span> {{ $order->delivery_note }}
                            </div>
                        @endif

                        <!-- List Item -->
                        <div class="border-t border-gray-100 pt-3 mb-3 space-y-2">
                            @foreach ($order->items as $item)
                                <div class="flex justify-between items-start text-sm">
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-gray-800">{{ $item->nama_produk_saat_beli }} <span class="text-[#B9232D]">x{{ $item->qty }}</span></span>
                                        
                                        <!-- Info Tipe Konsumsi / Meja -->
                                        @if ($item->consumption_type === 'dine_in')
                                            <span class="text-[10px] font-bold text-green-600 bg-green-50 px-1.5 py-0.5 rounded mt-1 inline-block w-max">
                                                Makan di tempat {{ $item->table_number ? '— Meja '.$item->table_number : '' }}
                                            </span>
                                        @elseif ($item->consumption_type === 'takeaway')
                                            <span class="text-[10px] font-medium text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded mt-1 inline-block w-max">
                                                Bungkus
                                            </span>
                                        @endif
                                    </div>
                                    <span class="font-bold text-gray-800">Rp{{ number_format($item->harga_saat_beli * $item->qty, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Total & Form Update Status -->
                        <div class="bg-gray-50 rounded-xl p-3">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-xs font-bold text-gray-500">Total Belanja</span>
                                <span class="text-base font-extrabold text-[#B9232D]">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>

                            <form method="POST" action="{{ route('dashboard.order.updateStatus', [$store->id, $order->id]) }}" class="flex gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="flex-grow bg-white border border-gray-200 text-gray-700 text-xs font-bold rounded-lg px-3 py-2 focus:outline-none focus:border-[#B9232D]">
                                    <option value="menunggu_konfirmasi" {{ $order->status === 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="dikonfirmasi" {{ $order->status === 'dikonfirmasi' ? 'selected' : '' }}>Dikonfirmasi</option>
                                    <option value="selesai" {{ $order->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="dibatalkan" {{ $order->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                                <button type="submit" class="bg-[#B9232D] text-white text-xs font-bold px-4 py-2 rounded-lg shadow-sm hover:bg-red-700 transition-colors">
                                    Update
                                </button>
                            </form>
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