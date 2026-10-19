<x-layouts.admin>
    <div x-data="{ showModal: false }">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Voucher Promo</h1>
                <p class="text-sm font-medium text-gray-500 mt-1">Kelola kode undangan dan diskon langganan.</p>
            </div>
            <button @click="showModal = true" class="inline-flex items-center gap-2 bg-[#B9232D] text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-red-500/30 hover:bg-red-800 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Voucher Baru
            </button>
        </div>

        @if(session('success'))
            <div class="mb-8 p-4 bg-green-50 border border-green-200 text-green-800 rounded-2xl text-sm font-bold flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Tabel Voucher -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 text-[11px] font-extrabold text-gray-400 uppercase tracking-widest bg-gray-50/50">
                        <th class="px-8 py-5">Kode Voucher</th>
                        <th class="px-6 py-5">Benefit</th>
                        <th class="px-6 py-5">Berlaku Sampai</th>
                        <th class="px-6 py-5">Status</th>
                        <th class="px-8 py-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($vouchers as $voucher)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-[#B9232D] font-extrabold text-sm border border-red-100">
                                        %
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-sm text-gray-900 tracking-wider">{{ $voucher->code }}</p>
                                        <p class="text-[11px] font-medium text-gray-500 mt-0.5">{{ $voucher->description ?? 'Tanpa deskripsi' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                    Gratis {{ $voucher->free_months }} Bulan
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <p class="font-bold text-sm text-gray-900">{{ $voucher->valid_until ? $voucher->valid_until->format('d M Y') : 'Selamanya' }}</p>
                            </td>
                            <td class="px-6 py-5">
                                <form method="POST" action="{{ route('admin.voucher.update', $voucher->id) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="is_active" value="{{ $voucher->is_active ? 0 : 1 }}">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[11px] font-bold {{ $voucher->is_active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-500 border border-gray-200' }} hover:opacity-80 transition-opacity">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $voucher->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                        {{ $voucher->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <form method="POST" action="{{ route('admin.voucher.destroy', $voucher->id) }}" onsubmit="return confirm('Yakin ingin menghapus voucher ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 bg-white border border-gray-200 hover:bg-red-50 p-2 rounded-xl transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center text-gray-500 text-sm">Belum ada voucher.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal Tambah Voucher -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-[100] flex items-end justify-center sm:items-center">
            <div x-show="showModal" x-transition.opacity class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="showModal = false"></div>
            
            <div x-show="showModal" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="translate-y-full sm:translate-y-8 sm:opacity-0"
                 x-transition:enter-end="translate-y-0 sm:opacity-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="translate-y-0 sm:opacity-100"
                 x-transition:leave-end="translate-y-full sm:translate-y-8 sm:opacity-0"
                 class="bg-white w-full max-w-md sm:rounded-3xl rounded-t-3xl shadow-2xl relative z-10 p-6 sm:p-8">
                
                <h3 class="text-xl font-extrabold text-gray-900 mb-6">Buat Voucher Baru</h3>
                
                <form method="POST" action="{{ route('admin.voucher.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Kode Voucher</label>
                            <input type="text" name="code" required placeholder="Contoh: UNDANGAN" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm uppercase font-bold text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#B9232D]/20 focus:border-[#B9232D]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Deskripsi (Opsional)</label>
                            <input type="text" name="description" placeholder="Promo Spesial" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#B9232D]/20 focus:border-[#B9232D]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Gratis Berapa Bulan?</label>
                            <input type="number" name="free_months" required value="3" min="1" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#B9232D]/20 focus:border-[#B9232D]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Berlaku Sampai (Opsional)</label>
                            <input type="date" name="valid_until" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#B9232D]/20 focus:border-[#B9232D]">
                        </div>
                    </div>
                    
                    <div class="flex gap-3 mt-8">
                        <button type="button" @click="showModal = false" class="w-full bg-gray-100 text-gray-700 font-bold py-3 rounded-xl hover:bg-gray-200 transition">Batal</button>
                        <button type="submit" class="w-full bg-[#B9232D] text-white font-bold py-3 rounded-xl hover:bg-red-800 transition shadow-lg shadow-red-500/30">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>