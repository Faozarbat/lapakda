<x-app-layout>
    <!-- Header Dashboard -->
    <header class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-gray-100">
        <div class="max-w-3xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-xl bg-[#B9232D] flex items-center justify-center text-white font-extrabold text-xl shadow-sm">
                    L
                </div>
                <span class="text-lg font-extrabold tracking-tight text-gray-900">Seller Center</span>
            </div>
            
            <div class="flex items-center gap-2">
                <!-- Tombol Khusus Admin -->
                @if(auth()->user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-center px-3 h-10 rounded-full bg-gray-900 text-white text-xs font-bold shadow-md hover:scale-105 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        Admin Panel
                    </a>
                @endif

                <!-- Tombol Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                <button type="submit" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-500 hover:text-red-500 hover:bg-red-50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                </button>
            </form>
        </div>
    </header>

    <div class="max-w-3xl mx-auto px-4 pt-6">
        
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-50 text-green-700 border border-green-100 rounded-xl text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <!-- Sapaan -->
        <div class="mb-6">
            <h1 class="text-xl font-extrabold text-gray-900">Halo, {{ Auth::user()->name }}!</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola toko dan pesananmu di sini.</p>
        </div>

        @php $stores = auth()->user()->stores; @endphp

        @if ($stores->isEmpty())
            <!-- Empty State -->
            <div class="text-center bg-white rounded-3xl p-10 border border-gray-100 shadow-sm mt-4">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Belum Ada Toko</h3>
                <p class="text-sm text-gray-500 mb-6">Kamu belum punya toko. Daftarkan tokomu sekarang dan mulai jualan!</p>
                <a href="{{ route('store.create') }}" class="inline-block bg-[#B9232D] text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg shadow-red-500/30 hover:scale-105 transition-transform">
                    + Daftarkan Toko Baru
                </a>
            </div>
        @else
            <!-- Daftar Toko -->
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-900">Toko Kamu</h2>
                <a href="{{ route('store.create') }}" class="text-xs font-bold text-[#B9232D] bg-red-50 px-3 py-1.5 rounded-lg hover:bg-red-100 transition-colors">
                    + Tambah Toko
                </a>
            </div>

            <div class="space-y-4">
                @foreach ($stores as $store)
                    <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
                        <!-- Header Card -->
                        <div class="flex justify-between items-start border-b border-gray-100 pb-3 mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-[#B9232D] font-extrabold text-lg">
                                    {{ substr($store->nama, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 leading-tight">{{ $store->nama }}</h3>
                                    <p class="text-[11px] text-gray-500 mt-0.5">{{ $store->kecamatan->nama }} • {{ $store->kategori === 'resto' ? 'Resto' : 'Toko' }}</p>
                                </div>
                            </div>
                            
                            <!-- Status Badge -->
                            <span class="bg-green-50 text-green-700 text-[10px] font-bold px-2 py-1 rounded-md border border-green-100">
                                Aktif
                            </span>
                        </div>

                        <!-- Menu Grid (Aksi Cepat) -->
                        <div class="grid grid-cols-4 gap-2">
                            <a href="{{ route('dashboard.produk.index', $store->id) }}" class="flex flex-col items-center justify-center p-2 rounded-xl hover:bg-gray-50 transition-colors group">
                                <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mb-1 group-hover:scale-110 transition-transform">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                </div>
                                <span class="text-[10px] font-bold text-gray-600">Produk</span>
                            </a>

                            <a href="{{ route('dashboard.order.index', $store->id) }}" class="flex flex-col items-center justify-center p-2 rounded-xl hover:bg-gray-50 transition-colors group">
                                <div class="w-8 h-8 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center mb-1 group-hover:scale-110 transition-transform">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                </div>
                                <span class="text-[10px] font-bold text-gray-600">Order</span>
                            </a>

                            <a href="{{ route('store.edit', $store->id) }}" class="flex flex-col items-center justify-center p-2 rounded-xl hover:bg-gray-50 transition-colors group">
                                <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center mb-1 group-hover:scale-110 transition-transform">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                </div>
                                <span class="text-[10px] font-bold text-gray-600">Setting</span>
                            </a>

                            <a href="{{ route('store.preview', $store->id) }}" class="flex flex-col items-center justify-center p-2 rounded-xl hover:bg-gray-50 transition-colors group">
                                <div class="w-8 h-8 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center mb-1 group-hover:scale-110 transition-transform">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </div>
                                <span class="text-[10px] font-bold text-gray-600">Lihat</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>