<section>
    <header class="mb-6">
        <h2 class="text-lg font-extrabold text-gray-900">
            Keamanan Akun
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            @if(auth()->user()->password_set_at)
                Perbarui password Anda secara berkala untuk menjaga keamanan.
            @else
                Anda belum membuat password. Buat sekarang agar akun Anda aman.
            @endif
        </p>
    </header>

    <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-4">
        @csrf
        @method('PUT')

        @if(auth()->user()->password_set_at)
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1.5">Password Saat Ini</label>
            <input type="password" name="current_password" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]">
            @error('current_password', 'updatePassword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        @endif

        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1.5">Password Baru</label>
            <input type="password" name="password" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]">
            @error('password', 'updatePassword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1.5">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]">
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="bg-[#B9232D] text-white font-bold py-2.5 px-6 rounded-xl hover:bg-red-800 transition-colors shadow-md shadow-red-500/20">
                Simpan Password
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm text-green-600 font-bold">
                    Berhasil disimpan!
                </p>
            @endif
        </div>
    </form>
</section>