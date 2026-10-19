<x-layouts.admin>
    <div class="mb-10">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Hasil Pencarian</h1>
        <p class="text-sm font-medium text-gray-500 mt-1">Menampilkan hasil untuk: "<strong>{{ $keyword }}</strong>"</p>
    </div>

    @if(!$keyword)
        <div class="bg-white p-10 rounded-3xl text-center border border-gray-100 text-gray-500">
            Silakan masukkan kata kunci di kolom pencarian atas.
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Hasil Toko -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="font-extrabold text-gray-900">Toko Ditemukan ({{ count($stores) }})</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($stores as $store)
                        <div class="p-5 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div>
                                <p class="font-bold text-sm text-gray-900">{{ $store->nama }}</p>
                                <p class="text-xs text-gray-500">{{ $store->kecamatan->nama }}</p>
                            </div>
                            <a href="{{ route('store.show', [$store->kecamatan->slug, $store->slug]) }}" target="_blank" class="text-xs font-bold text-[#B9232D] hover:underline">Lihat Toko &rarr;</a>
                        </div>
                    @empty
                        <div class="p-5 text-sm text-gray-500">Tidak ada toko yang cocok.</div>
                    @endforelse
                </div>
            </div>

            <!-- Hasil User -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="font-extrabold text-gray-900">Pemilik Ditemukan ({{ count($users) }})</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($users as $user)
                        <div class="p-5 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div>
                                <p class="font-bold text-sm text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->nomor_hp ?? 'Tidak ada no HP' }}</p>
                            </div>
                            <span class="text-[10px] font-bold bg-gray-100 px-2 py-1 rounded text-gray-600">{{ $user->is_admin ? 'Admin' : 'User' }}</span>
                        </div>
                    @empty
                        <div class="p-5 text-sm text-gray-500">Tidak ada pemilik yang cocok.</div>
                    @endforelse
                </div>
            </div>

        </div>
    @endif
</x-layouts.admin>