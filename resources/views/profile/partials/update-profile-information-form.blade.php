<section>
    <header class="mb-4">
        <h2 class="text-base font-extrabold text-gray-900">
            Informasi Akun
        </h2>
        <p class="mt-1 text-xs text-gray-500">
            Perbarui nama dan nomor WhatsApp kamu.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-xs font-bold text-gray-700 mb-1.5">Nama Lengkap</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]">
            <x-input-error class="mt-1 text-red-500 text-xs" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="nomor_hp" class="block text-xs font-bold text-gray-700 mb-1.5">Nomor WhatsApp</label>
            <input id="nomor_hp" name="nomor_hp" type="tel" value="{{ old('nomor_hp', $user->nomor_hp) }}" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]">
            <x-input-error class="mt-1 text-red-500 text-xs" :messages="$errors->get('nomor_hp')" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="bg-[#B9232D] text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-md shadow-red-500/20 hover:scale-105 transition-transform">
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-xs font-bold text-green-600">
                    Berhasil disimpan!
                </p>
            @endif
        </div>
    </form>
</section>