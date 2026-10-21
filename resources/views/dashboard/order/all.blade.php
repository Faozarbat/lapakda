<x-app-layout>
    <!-- Header -->
    <header class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-gray-100">
        <div class="max-w-3xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-700 hover:bg-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
            </a>
            <h1 class="font-bold text-base text-gray-900 absolute left-1/2 -translate-x-1/2">Semua Pesanan</h1>
            <div class="w-10"></div>
        </div>
    </header>

    <div class="max-w-3xl mx-auto px-4 pt-6 pb-24">
        @if ($orders->isEmpty())
            <div class="text-center bg-white rounded-3xl p-10 border border-gray-100 shadow-sm mt-4">
                <p class="text-sm text-gray-500">Belum ada pesanan masuk.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($orders as $order)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                        <!-- Info Toko -->
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
                                {{ strtoupper($order->status) }}
                            </span>
                        </div>

                        <div class="text-sm font-bold text-[#B9232D] mb-3">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </div>

                        <!-- Tombol Aksi -->
                        <a href="{{ route('dashboard.order.index', $order->store_id) }}" class="block w-full text-center bg-gray-100 text-gray-700 text-xs font-bold py-2.5 rounded-xl hover:bg-gray-200 transition-colors">
                            Lihat Detail di Toko
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</x-app-layout>