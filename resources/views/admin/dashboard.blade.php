<x-layouts.admin>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Dashboard Overview</h1>
            <p class="text-sm font-medium text-gray-500 mt-1">Pantau performa dan aktivitas LapakDA secara keseluruhan.</p>
        </div>
        <a href="{{ route('admin.subscription.index') }}" class="inline-flex items-center gap-2 bg-[#B9232D] text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-red-500/30 hover:bg-red-800 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Verifikasi Tagihan
        </a>
    </div>

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-[0_4px_20px_rgb(0,0,0,0.02)] relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-4 relative z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <p class="text-sm font-bold text-gray-500 mb-1 relative z-10">Total Toko</p>
            <p class="text-3xl font-extrabold text-gray-900 relative z-10">{{ $totalStores }}</p>
        </div>
        
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-[0_4px_20px_rgb(0,0,0,0.02)] relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-purple-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center mb-4 relative z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <p class="text-sm font-bold text-gray-500 mb-1 relative z-10">Total User</p>
            <p class="text-3xl font-extrabold text-gray-900 relative z-10">{{ $totalUsers }}</p>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-[0_4px_20px_rgb(0,0,0,0.02)] relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-orange-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center mb-4 relative z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <p class="text-sm font-bold text-gray-500 mb-1 relative z-10">Total Order</p>
            <p class="text-3xl font-extrabold text-gray-900 relative z-10">{{ $totalOrders }}</p>
        </div>

        <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-6 rounded-3xl shadow-xl shadow-gray-900/20 relative overflow-hidden group text-white">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-bl-full -mr-6 -mt-6 transition-transform group-hover:scale-110"></div>
            <div class="w-12 h-12 bg-white/10 text-white rounded-2xl flex items-center justify-center mb-4 relative z-10 backdrop-blur-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <p class="text-sm font-bold text-gray-300 mb-1 relative z-10">Pendapatan (Bln Ini)</p>
            <p class="text-3xl font-extrabold relative z-10">Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart Area -->
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-gray-100 shadow-[0_4px_20px_rgb(0,0,0,0.02)]">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-extrabold text-gray-900 text-lg">Pendaftaran Toko</h2>
                <span class="text-xs font-bold text-gray-400 bg-gray-50 px-3 py-1 rounded-lg border border-gray-100">7 Hari Terakhir</span>
            </div>
            <div id="chart"></div>
        </div>

        <!-- Toko Terbaru -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-[0_4px_20px_rgb(0,0,0,0.02)] flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-extrabold text-gray-900 text-lg">Toko Terbaru</h2>
            </div>
            <div class="space-y-4 flex-1">
                @forelse($recentStores as $store)
                    <div class="flex items-start justify-between border-b border-gray-50 pb-4 last:border-0 last:pb-0 group cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 border border-gray-100 text-gray-500 flex items-center justify-center font-extrabold text-sm group-hover:bg-red-50 group-hover:text-[#B9232D] group-hover:border-red-100 transition-colors">
                                {{ substr($store->nama, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-sm text-gray-900 group-hover:text-[#B9232D] transition-colors">{{ $store->nama }}</p>
                                <p class="text-[11px] text-gray-500 mt-0.5">{{ $store->kecamatan->nama }}</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-gray-400 whitespace-nowrap bg-gray-50 px-2 py-1 rounded-md">{{ $store->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-10">Belum ada toko.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ApexCharts Script -->
    <script>
        var options = {
            series: [{
                name: 'Toko Baru',
                data: @json($chartData)
            }],
            chart: {
                type: 'area',
                height: 300,
                fontFamily: 'Plus Jakarta Sans, sans-serif',
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            colors: ['#B9232D'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.3,
                    opacityTo: 0,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 4, lineCap: 'round' },
            markers: {
                size: 4,
                colors: ['#fff'],
                strokeColors: '#B9232D',
                strokeWidth: 2,
                hover: { size: 6 }
            },
            xaxis: {
                categories: @json($chartLabels),
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: '#9CA3AF', fontSize: '12px', fontWeight: 600 } }
            },
            yaxis: {
                labels: { style: { colors: '#9CA3AF', fontSize: '12px', fontWeight: 600 } }
            },
            grid: {
                borderColor: '#F3F4F6',
                strokeDashArray: 4,
                yaxis: { lines: { show: true } },
                xaxis: { lines: { show: false } }
            },
            tooltip: {
                theme: 'light',
                y: { formatter: function (val) { return val + " Toko" } }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>
</x-layouts.admin>