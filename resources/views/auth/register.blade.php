<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-extrabold text-gray-900">Daftar Akun</h2>
        <p class="text-sm text-gray-500 mt-1">Cukup isi nama dan nomor WA.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <label for="name" class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]" required autofocus>
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500 text-xs" />
        </div>

        <div>
            <label for="nomor_hp" class="block text-sm font-bold text-gray-700 mb-1">Nomor WhatsApp</label>
            <input id="nomor_hp" type="tel" name="nomor_hp" value="{{ old('nomor_hp') }}" placeholder="Contoh: 0812..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D] focus:border-[#B9232D]" required>
            <x-input-error :messages="$errors->get('nomor_hp')" class="mt-2 text-red-500 text-xs" />
        </div>

        <button type="submit" class="w-full bg-[#B9232D] text-white py-3.5 rounded-xl text-sm font-bold shadow-lg shadow-red-500/30 hover:scale-[1.02] transition-transform mt-2">
            Daftar Sekarang
        </button>

         <p class="text-center text-[11px] text-gray-500 mt-3 leading-relaxed">
            Dengan mendaftar, kamu menyetujui <br> 
            <a href="{{ route('terms') }}" target="_blank" class="font-bold text-[#B9232D] hover:underline">Syarat & Ketentuan</a> serta <a href="{{ route('privacy.policy') }}" target="_blank" class="font-bold text-[#B9232D] hover:underline">Kebijakan Privasi</a> kami.
        </p>

        <p class="text-center text-sm text-gray-500 mt-4">
            Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-[#B9232D]">Masuk</a>
        </p>
    </form>
</x-guest-layout>