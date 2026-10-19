<section class="space-y-4">
    <header>
        <h2 class="text-base font-extrabold text-red-700">
            Hapus Akun
        </h2>
        <p class="mt-1 text-xs text-red-600/80 leading-relaxed">
            Setelah akun dihapus, semua data riwayat pesananmu akan hilang permanen. Tindakan ini tidak dapat dibatalkan.
        </p>
    </header>

    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="bg-white border border-red-200 text-red-600 px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm hover:bg-red-50 transition-colors">
        Hapus Akun Saya
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-extrabold text-gray-900 mb-2">
                Yakin ingin menghapus akun?
            </h2>

            <p class="text-sm text-gray-500 mb-4">
                Masukkan password akunmu untuk mengonfirmasi penghapusan permanen.
                <br><span class="text-xs italic text-red-500">(Jika kamu tidak pernah membuat password, coba gunakan password bawaan atau hubungi admin).</span>
            </p>

            <div class="mt-4">
                <input id="password" name="password" type="password" placeholder="Password" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500">
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-red-500 text-xs" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-5 py-2.5 rounded-xl text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                    Batal
                </button>

                <button type="submit" class="bg-red-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-md shadow-red-500/20 hover:bg-red-700 transition-colors">
                    Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>