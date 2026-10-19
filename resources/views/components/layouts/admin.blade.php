<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — LapakDA</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } [x-cloak] { display: none !important; } </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body class="bg-[#F4F6F8] text-gray-800 antialiased" x-data="{ sidebarOpen: false }">

    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-40 lg:hidden" @click="sidebarOpen = false" x-cloak></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col">
        <!-- Logo Area -->
        <div class="h-20 flex items-center px-8">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#B9232D] to-red-600 flex items-center justify-center text-white font-extrabold text-lg shadow-lg shadow-red-500/30">
                L
            </div>
            <span class="ml-3 font-extrabold tracking-tight text-xl text-gray-900">LapakDA</span>
            <span class="ml-2 bg-red-50 text-[#B9232D] text-[10px] font-extrabold px-2 py-1 rounded-md uppercase tracking-wider">Admin</span>
        </div>

        <!-- Menu -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <p class="px-4 text-[11px] font-extrabold text-gray-400 uppercase tracking-widest mb-3">Menu Utama</p>
            
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('admin.dashboard') ? 'bg-[#B9232D] text-white shadow-md shadow-red-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 font-medium' }} transition-all">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span class="text-sm font-semibold">Dashboard</span>
            </a>
            
            <a href="{{ route('admin.store.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('admin.store.*') ? 'bg-[#B9232D] text-white shadow-md shadow-red-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 font-medium' }} transition-all">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.store.*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <span class="text-sm font-semibold">Manajemen Toko</span>
            </a>

            <a href="{{ route('admin.subscription.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('admin.subscription.*') ? 'bg-[#B9232D] text-white shadow-md shadow-red-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 font-medium' }} transition-all">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.subscription.*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span class="text-sm font-semibold">Verifikasi Tagihan</span>
            </a>

            <a href="{{ route('admin.voucher.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('admin.voucher.*') ? 'bg-[#B9232D] text-white shadow-md shadow-red-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 font-medium' }} transition-all">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.voucher.*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                <span class="text-sm font-semibold">Voucher Promo</span>
            </a>

            <a href="{{ route('admin.setting.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('admin.setting.*') ? 'bg-[#B9232D] text-white shadow-md shadow-red-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 font-medium' }} transition-all">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.setting.*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="text-sm font-semibold">Pengaturan Harga</span>
            </a>
        </nav>

        <!-- User Profile (Bottom) -->
        <div class="p-4 border-t border-gray-100">
            <div class="bg-gray-50 rounded-2xl p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-[#B9232D] font-extrabold text-sm shadow-sm">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 truncate max-w-[100px]">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-gray-500 font-medium">Super Admin</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:pl-72 flex flex-col min-h-screen">
        
        <!-- Topbar -->
        <!-- Header dijadikan fixed/sticky dengan form pencarian nyata -->
        <header class="bg-white/80 backdrop-blur-xl h-20 flex items-center justify-between px-6 sm:px-10 sticky top-0 z-30 border-b border-gray-100/50">
            <div class="flex items-center gap-4 flex-1">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                
                <!-- Search Bar Nyata -->
                <form action="{{ route('admin.search') }}" method="GET" class="hidden md:flex items-center bg-gray-100/80 px-4 py-2.5 rounded-2xl w-full max-w-md border border-transparent focus-within:border-[#B9232D]/50 focus-within:bg-white focus-within:shadow-sm transition-all">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama toko atau user..." class="bg-transparent border-none outline-none text-sm w-full ml-2 text-gray-700 placeholder-gray-400 focus:ring-0">
                </form>
            </div>

            <div class="flex items-center gap-4">
                <button class="relative p-2 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                </button>
            </div>
        </header>

        <!-- Konten -->
        <main class="flex-1 p-4 sm:p-6 lg:p-10 max-w-7xl mx-auto w-full">
            {{ $slot }}
        </main>
    </div>

</body>
</html>