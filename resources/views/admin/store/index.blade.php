<x-layouts.admin>
    <div class="mb-10">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen Toko</h1>
        <p class="text-sm font-medium text-gray-500 mt-1">Pantau dan kelola seluruh toko yang terdaftar di LapakDA.</p>
    </div>

    @if(session('success'))
        <div class="mb-8 p-4 bg-green-50 border border-green-200 text-green-800 rounded-2xl text-sm font-bold flex items-center gap-3">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 text-[11px] font-extrabold text-gray-400 uppercase tracking-widest bg-gray-50/50">
                        <th class="px-8 py-5">Nama Toko</th>
                        <th class="px-6 py-5">Pemilik</th>
                        <th class="px-6 py-5">Langganan</th>
                        <th class="px-6 py-5">Status Toko</th>
                        <th class="px-8 py-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($stores as $store)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-11 h-11 rounded-2xl bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-700 font-extrabold text-sm flex-shrink-0 group-hover:scale-105 transition-transform">
                                        {{ substr($store->nama, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-sm text-gray-900">{{ $store->nama }}</p>
                                        <p class="text-[11px] font-medium text-gray-500 mt-0.5">{{ $store->kecamatan->nama }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <p class="font-bold text-sm text-gray-900">{{ $store->owner->name ?? '-' }}</p>
                                <p class="text-[11px] font-medium text-gray-500">{{ $store->owner->nomor_hp ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ $store->subscription_status === 'active' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                    {{ $store->subscription_status }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[11px] font-bold {{ $store->status_aktif ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-500 border border-gray-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $store->status_aktif ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                    {{ $store->status_aktif ? 'Buka' : 'Ditutup / Suspend' }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <form method="POST" action="{{ route('admin.store.toggle', $store->id) }}">
                                    @csrf @method('PATCH')
                                    @if($store->status_aktif)
                                        <button type="submit" class="bg-white border border-red-200 text-red-600 hover:bg-red-50 text-[11px] font-bold uppercase tracking-wide px-4 py-2 rounded-xl transition-colors">Suspend</button>
                                    @else
                                        <button type="submit" class="bg-white border border-green-200 text-green-600 hover:bg-green-50 text-[11px] font-bold uppercase tracking-wide px-4 py-2 rounded-xl transition-colors">Aktifkan</button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center text-gray-500 text-sm">Belum ada toko terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Paginasi -->
        @if($stores->hasPages())
            <div class="p-6 border-t border-gray-100 bg-gray-50/50">
                {{ $stores->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>