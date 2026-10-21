@auth
    @php
        $storeIds = auth()->user()->stores()->pluck('id');
        $pendingCount = \App\Models\Order::whereIn('store_id', $storeIds)->where('status', 'menunggu_konfirmasi')->count();
    @endphp
@endauth
<div class="fixed bottom-4 left-4 right-4 z-50 flex justify-center pointer-events-none">
    <nav class="bg-white/90 backdrop-blur-xl border border-gray-200/60 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.08)] px-1 py-1.5 flex items-center justify-between w-full max-w-md pointer-events-auto">
        
        <!-- Beranda -->
        <a href="{{ route('home') }}" class="flex-1 flex flex-col items-center justify-center gap-1 py-2 {{ request()->routeIs('home') ? 'text-[#B9232D]' : 'text-gray-400 hover:text-gray-800' }} transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
            </svg>
            <span class="text-[10px] {{ request()->routeIs('home') ? 'font-bold' : 'font-medium' }}">Beranda</span>
        </a>

        <!-- Jelajah -->
        <a href="{{ route('jelajah', 'resto') }}" class="flex-1 flex flex-col items-center justify-center gap-1 py-2 {{ request()->routeIs('jelajah') ? 'text-[#B9232D]' : 'text-gray-400 hover:text-gray-800' }} transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
            <span class="text-[10px] {{ request()->routeIs('jelajah') ? 'font-bold' : 'font-medium' }}">Jelajah</span>
        </a>

        <!-- Keranjang -->
        <a href="{{ route('cart.index') }}" class="flex-1 flex flex-col items-center justify-center gap-1 py-2 {{ request()->routeIs('cart.*') ? 'text-[#B9232D]' : 'text-gray-400 hover:text-gray-800' }} transition-colors relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3z" />
            </svg>
            <span class="text-[10px] {{ request()->routeIs('cart.*') ? 'font-bold' : 'font-medium' }}">Keranjang</span>
        </a>

        <!-- Pesanan (Terpadu) -->
        <a href="{{ route('order.riwayat') }}" class="flex-1 flex flex-col items-center justify-center gap-1 py-2 relative {{ request()->routeIs('order.riwayat') ? 'text-[#B9232D]' : 'text-gray-400 hover:text-gray-800' }} transition-colors">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                @auth
                    @if(isset($pendingCount) && $pendingCount > 0)
                        <span class="absolute -top-1.5 -right-2 bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full border-2 border-white">
                            {{ $pendingCount }}
                        </span>
                    @endif
                @endauth
            </div>
            <span class="text-[10px] {{ request()->routeIs('dashboard.order.*') || request()->routeIs('order.riwayat') ? 'font-bold' : 'font-medium' }}">Pesanan</span>
        </a>

        <!-- Dashboard (Pengganti Akun) -->
        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="flex-1 flex flex-col items-center justify-center gap-1 py-2 {{ request()->routeIs('dashboard') || request()->routeIs('store.*') ? 'text-[#B9232D]' : 'text-gray-400 hover:text-gray-800' }} transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
            </svg>
            <span class="text-[10px] {{ request()->routeIs('dashboard') || request()->routeIs('store.*') ? 'font-bold' : 'font-medium' }}">Dashboard</span>
        </a>
    </nav>
</div>