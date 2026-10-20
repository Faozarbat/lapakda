<x-layouts.admin>
    <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen User</h1>
            <p class="text-sm font-medium text-gray-500 mt-1">Daftar seluruh akun pembeli dan seller yang terdaftar.</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 text-[11px] font-extrabold text-gray-400 uppercase tracking-widest bg-gray-50/50">
                        <th class="px-8 py-5">Nama User</th>
                        <th class="px-6 py-5">Nomor HP (WA)</th>
                        <th class="px-6 py-5">Peran</th>
                        <th class="px-6 py-5">Jumlah Toko</th>
                        <th class="px-8 py-5 text-right">Bergabung</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-11 h-11 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-700 font-extrabold text-sm flex-shrink-0 group-hover:scale-105 transition-transform">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-sm text-gray-900">{{ $user->name }}</p>
                                        <p class="text-[11px] font-medium text-gray-500 mt-0.5">{{ $user->email ?? 'Tidak ada email' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                @if($user->nomor_hp)
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', $user->nomor_hp) }}" target="_blank" class="inline-flex items-center gap-1.5 text-sm font-bold text-gray-700 hover:text-green-600 transition-colors">
                                        {{ $user->nomor_hp }}
                                    </a>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-5">
                                @if($user->is_admin)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-100">
                                        Admin
                                    </span>
                                @elseif($user->stores_count > 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-100">
                                        Seller
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-600 border border-gray-200">
                                        Pembeli
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-5">
                                <p class="font-bold text-sm text-gray-900">{{ $user->stores_count }} Toko</p>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <p class="text-xs font-bold text-gray-500">{{ $user->created_at->format('d M Y') }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center text-gray-500 text-sm">Belum ada user terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Paginasi -->
        @if($users->hasPages())
            <div class="p-6 border-t border-gray-100 bg-gray-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>