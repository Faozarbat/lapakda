<x-layouts.admin>
    <!-- Header Halaman -->
    <div class="mb-10">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Verifikasi Pembayaran</h1>
        <p class="text-sm font-medium text-gray-500 mt-1">Kelola konfirmasi tagihan seller yang masuk.</p>
    </div>

    @if(session('success'))
        <div class="mb-8 p-4 bg-green-50/80 border border-green-200 text-green-800 rounded-2xl text-sm font-bold flex items-center gap-3 shadow-sm">
            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            {{ session('success') }}
        </div>
    @endif

    <!-- Card Container (Table Modern) -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
        
        <!-- Versi Desktop -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 text-[11px] font-extrabold text-gray-400 uppercase tracking-widest bg-gray-50/50">
                        <th class="px-8 py-5">Toko / Pemilik</th>
                        <th class="px-6 py-5">Jenis Tagihan</th>
                        <th class="px-6 py-5">Nominal</th>
                        <th class="px-6 py-5">Bukti Transfer</th>
                        <th class="px-8 py-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($subscriptions as $sub)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 flex items-center justify-center text-gray-700 font-extrabold text-sm flex-shrink-0 group-hover:scale-105 transition-transform">
                                        {{ substr($sub->store->nama, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-sm text-gray-900">{{ $sub->store->nama }}</p>
                                        <p class="text-[12px] font-medium text-gray-500 mt-0.5">{{ $sub->user->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[11px] font-bold {{ $sub->type === 'open_fee' ? 'bg-orange-50 text-orange-700 border border-orange-100' : 'bg-blue-50 text-blue-700 border border-blue-100' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $sub->type === 'open_fee' ? 'bg-orange-500' : 'bg-blue-500' }}"></span>
                                    {{ $sub->type === 'open_fee' ? 'Biaya Buka' : 'Langganan' }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <p class="font-extrabold text-sm text-gray-900">Rp {{ number_format($sub->amount, 0, ',', '.') }}</p>
                            </td>
                            <td class="px-6 py-5">
                                @if($sub->payment_proof)
                                    <a href="{{ Storage::url($sub->payment_proof) }}" target="_blank" class="inline-flex items-center gap-2 text-[11px] font-bold text-gray-600 bg-white border border-gray-200 hover:border-gray-300 hover:bg-gray-50 px-3 py-2 rounded-xl transition-all shadow-sm">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Lihat
                                    </a>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-[11px] text-red-500 font-bold bg-red-50 border border-red-100 px-3 py-2 rounded-xl">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Belum Upload
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-right">
                                <form method="POST" action="{{ route('admin.subscription.approve', $sub->id) }}">
                                    @csrf
                                    <button type="submit" class="bg-gray-900 text-white text-[11px] font-bold uppercase tracking-wide px-5 py-2.5 rounded-xl hover:bg-green-600 hover:shadow-lg hover:shadow-green-500/30 transition-all">Verifikasi</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-24 text-center">
                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <h3 class="text-lg font-extrabold text-gray-900 mb-1">Bersih Tanpa Tugas</h3>
                                <p class="text-sm text-gray-500">Saat ini belum ada tagihan yang perlu diverifikasi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Versi Mobile (Cards) -->
        <div class="md:hidden divide-y divide-gray-100">
            @forelse($subscriptions as $sub)
                <div class="p-6 space-y-4 hover:bg-gray-50 transition-colors">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-700 font-extrabold text-base">
                                {{ substr($sub->store->nama, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-extrabold text-sm text-gray-900">{{ $sub->store->nama }}</p>
                                <p class="text-xs font-medium text-gray-500">{{ $sub->user->name }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $sub->type === 'open_fee' ? 'bg-orange-50 text-orange-700' : 'bg-blue-50 text-blue-700' }}">
                            {{ $sub->type === 'open_fee' ? 'Buka' : 'Langganan' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center bg-white border border-gray-100 p-3.5 rounded-xl shadow-sm">
                        <p class="font-extrabold text-[#B9232D]">Rp {{ number_format($sub->amount, 0, ',', '.') }}</p>
                        @if($sub->payment_proof)
                            <a href="{{ Storage::url($sub->payment_proof) }}" target="_blank" class="text-xs font-bold text-blue-600 underline">Lihat Bukti</a>
                        @else
                            <span class="text-xs text-red-500 font-bold">Belum Upload</span>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('admin.subscription.approve', $sub->id) }}">
                        @csrf
                        <button type="submit" class="w-full bg-gray-900 text-white text-xs font-bold py-3.5 rounded-xl hover:bg-green-600 transition-colors shadow-md">Verifikasi Sekarang</button>
                    </form>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-extrabold text-gray-900 mb-1">Bersih Tanpa Tugas</h3>
                    <p class="text-sm text-gray-500">Belum ada tagihan yang perlu diverifikasi.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.admin>