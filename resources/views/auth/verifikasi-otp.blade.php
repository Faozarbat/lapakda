<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Kode OTP sudah dikirim lewat WhatsApp ke nomor <strong>{{ $nomorHp }}</strong>.
        Masukkan 6 digit kodenya di bawah ini.
    </div>

    @if (session('success'))
        <div class="mb-4 text-sm text-green-600 font-medium">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('otp.verify') }}">
        @csrf

        <div>
            <x-input-label for="kode" value="Kode OTP" />
            <x-text-input id="kode" type="text" name="kode" inputmode="numeric" pattern="[0-9]*" maxlength="6"
                           class="block mt-1 w-full text-center text-2xl tracking-[0.5em] font-semibold"
                           autofocus required autocomplete="one-time-code" />
            <x-input-error :messages="$errors->get('kode')" class="mt-2" />
        </div>

        <div class="mt-5">
            <x-primary-button class="w-full justify-center">
                Verifikasi
            </x-primary-button>
        </div>
    </form>

    <form method="POST" action="{{ route('otp.resend') }}" class="mt-4 text-center">
        @csrf
        <button type="submit" id="btnKirimUlang" class="text-sm text-gray-600 underline">
            Kirim ulang kode
        </button>
    </form>

    <script>
        const btn = document.getElementById('btnKirimUlang');
        const labelAsli = btn.textContent;

        btn.addEventListener('click', function () {
            let detik = 60;
            btn.disabled = true;

            const interval = setInterval(() => {
                btn.textContent = `Kirim ulang (${detik}s)`;
                detik--;
                if (detik < 0) {
                    clearInterval(interval);
                    btn.disabled = false;
                    btn.textContent = labelAsli;
                }
            }, 1000);
        });
    </script>
</x-guest-layout>
