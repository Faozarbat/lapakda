<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-extrabold text-gray-900">Masuk ke LapakDA</h2>
        <p class="text-sm text-gray-500 mt-1">Masukkan nomor WhatsApp kamu.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label for="nomor_hp" class="block text-sm font-bold text-gray-700 mb-1">Nomor WhatsApp</label>
            <input id="nomor_hp" type="tel" name="nomor_hp" value="{{ old('nomor_hp') }}" placeholder="Contoh: 0812..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]" required autofocus>
            <x-input-error :messages="$errors->get('nomor_hp')" class="mt-2 text-red-500 text-xs" />
        </div>

        <div>
            <label for="password" class="block text-sm font-bold text-gray-700 mb-1">Password (masukkan jika ada)</label>
            <input id="password" type="password" name="password" placeholder="Biarkan kosong jika tidak ada" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-xs" />
        </div>

        <button type="submit" class="w-full bg-[#B9232D] text-white py-3.5 rounded-xl text-sm font-bold shadow-lg shadow-red-500/30 hover:scale-[1.02] transition-transform">
            Masuk
        </button>

        <p class="text-center text-sm text-gray-500 mt-4">
            Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-[#B9232D]">Daftar</a>
        </p>
    </form>
</x-guest-layout>