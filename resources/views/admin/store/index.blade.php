<x-layouts.admin>
    <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen Toko</h1>
            <p class="text-sm font-medium text-gray-500 mt-1">Pantau dan kelola seluruh toko yang terdaftar di LapakDA.</p>
        </div>
        <a href="{{ route('admin.store.export') }}" class="inline-flex items-center justify-center gap-2 bg-white border border-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm hover:bg-gray-50 transition-all">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Export CSV
        </a>
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
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Tombol WA Reminder -->
                                    @if($store->owner && $store->owner->nomor_hp)
                                        @php
                                            // Format nomor HP ke standar WA (ubah 0 menjadi 62)
                                            $waNumber = preg_replace('/^0/', '62', $store->owner->nomor_hp);
                                            $pesan = "Halo " . $store->owner->name . ", ini Admin LapakDA. Mengingatkan bahwa masa aktif toko *" . $store->nama . "* akan berakhir pada *" . ($store->active_until ? $store->active_until->format('d M Y') : '-') . "*. Silakan perpanjang langganan Anda agar toko tetap tampil di aplikasi. Terima kasih!";
                                        @endphp
                                        <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode($pesan) }}" target="_blank" class="bg-green-50 border border-green-200 text-green-600 hover:bg-green-100 p-2 rounded-xl transition-colors" title="Ingatkan via WA">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                        </a>
                                    @endif

                                    <form method="POST" action="{{ route('admin.store.toggle', $store->id) }}">
                                        @csrf @method('PATCH')
                                        @if($store->status_aktif)
                                            <button type="submit" class="bg-white border border-red-200 text-red-600 hover:bg-red-50 text-[11px] font-bold uppercase tracking-wide px-4 py-2 rounded-xl transition-colors">Suspend</button>
                                        @else
                                            <button type="submit" class="bg-white border border-green-200 text-green-600 hover:bg-green-50 text-[11px] font-bold uppercase tracking-wide px-4 py-2 rounded-xl transition-colors">Aktifkan</button>
                                        @endif
                                    </form>
                                </div>
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