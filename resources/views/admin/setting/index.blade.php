<x-layouts.admin>
    <div class="mb-10">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Pengaturan Harga</h1>
        <p class="text-sm font-medium text-gray-500 mt-1">Atur nominal biaya untuk seluruh sistem LapakDA.</p>
    </div>

    @if(session('success'))
        <div class="mb-8 p-4 bg-green-50 border border-green-200 text-green-800 rounded-2xl text-sm font-bold flex items-center gap-3">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-2xl bg-white rounded-3xl border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8">
        <form method="POST" action="{{ route('admin.setting.update') }}">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Biaya Buka Toko (Resto ke-2 dst) dalam Rupiah</label>
                    <input type="number" name="open_fee_price" value="{{ old('open_fee_price', $settings['open_fee_price']) }}" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#B9232D]/20 focus:border-[#B9232D] transition-all font-bold text-gray-900">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Harga Langganan Normal per bulan (dalam Rupiah)</label>
                    <input type="number" name="monthly_price" value="{{ old('monthly_price', $settings['monthly_price']) }}" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#B9232D]/20 focus:border-[#B9232D] transition-all font-bold text-gray-900">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Harga Diskon 50% Bulan ke 4-6 (dalam Rupiah)</label>
                    <input type="number" name="discount_price" value="{{ old('discount_price', $settings['discount_price']) }}" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#B9232D]/20 focus:border-[#B9232D] transition-all font-bold text-gray-900">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Masa Tenggang (Grace Period) - Hari</label>
                    <input type="number" name="grace_period_days" value="{{ old('grace_period_days', $settings['grace_period_days']) }}" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#B9232D]/20 focus:border-[#B9232D] transition-all font-bold text-gray-900">
                    <p class="text-xs text-gray-500 mt-2">Toko masih tampil di publik selama masa tenggang ini sebelum akhirnya diarsipkan.</p>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <button type="submit" class="w-full bg-[#B9232D] text-white font-bold py-3.5 rounded-xl shadow-lg shadow-red-500/30 hover:bg-red-800 transition-all">Simpan Pengaturan</button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>