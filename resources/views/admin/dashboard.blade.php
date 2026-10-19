<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#B9232D">
    <title>Admin Panel — LapakDA</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F9FA] text-gray-800 antialiased pb-10">

    <!-- HEADER -->
    <header class="bg-gray-900 text-white sticky top-0 z-40 border-b border-gray-800">
        <div class="max-w-4xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-[#B9232D] flex items-center justify-center font-bold text-lg">A</div>
                <span class="font-extrabold tracking-tight text-lg">Admin Panel</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs font-bold text-gray-300 hover:text-white">Logout</button>
            </form>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 pt-6">
        <h1 class="text-xl font-extrabold text-gray-900 mb-1">Ringkasan Sistem</h1>
        <p class="text-sm text-gray-500 mb-6">Pantau aktivitas LapakDA secara keseluruhan.</p>

        <!-- Statistik -->
        <div class="grid grid-cols-3 gap-3 mb-8">
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm text-center">
                <p class="text-[10px] font-bold text-gray-400 uppercase">Total Toko</p>
                <p class="text-2xl font-extrabold text-[#B9232D] mt-1">{{ $totalStores }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm text-center">
                <p class="text-[10px] font-bold text-gray-400 uppercase">Total User</p>
                <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ $totalUsers }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm text-center">
                <p class="text-[10px] font-bold text-gray-400 uppercase">Total Order</p>
                <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ $totalOrders }}</p>
            </div>
        </div>

        <!-- Pendaftaran Toko Terbaru -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-bold text-gray-900">Pendaftaran Toko Terbaru</h2>
            </div>
            
            <div class="space-y-3">
                @forelse($recentStores as $store)
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3 last:border-0 last:pb-0">
                        <div>
                            <p class="font-bold text-sm text-gray-900">{{ $store->nama }}</p>
                            <p class="text-xs text-gray-500">Oleh: {{ $store->owner->name ?? 'Tidak diketahui' }} • {{ $store->kecamatan->nama }}</p>
                        </div>
                        <span class="bg-green-50 text-green-700 text-[10px] font-bold px-2 py-1 rounded-md border border-green-100">
                            Aktif
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada toko terdaftar.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 text-sm text-blue-800">
            <strong>Info:</strong> Fitur moderasi (suspend toko, kelola kategori) akan ditambahkan di tahap selanjutnya.
        </div>
    </main>
</body>
</html>