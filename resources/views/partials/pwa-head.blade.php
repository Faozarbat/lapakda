<link rel="manifest" href="{{ asset('manifest.json') }}">
<meta name="theme-color" content="#d32f2f">
<link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').catch(() => {
                // Gagal daftar SW tidak boleh bikin halaman error — cukup diam,
                // situs tetap berfungsi normal tanpa PWA aktif.
            });
        });
    }
</script>