<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#B9232D">
    <title>Syarat & Ketentuan — LapakDA</title>
    
    @include('partials.pwa-head')
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F9FA] text-gray-800 antialiased pb-10">

    <!-- HEADER -->
    <header class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-gray-100">
        <div class="max-w-3xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ url()->previous() }}" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-700 hover:bg-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
            </a>
            <h1 class="font-bold text-base text-gray-900 absolute left-1/2 -translate-x-1/2">Syarat & Ketentuan</h1>
            <div class="w-10"></div>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 pt-6 pb-20">
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-6 text-sm leading-relaxed text-gray-600">
            
            <div>
                <h2 class="text-lg font-extrabold text-gray-900 mb-2">1. Batasan Tanggung Jawab Platform</h2>
                <p>LapakDA adalah platform katalog online yang berfungsi sebagai <strong>etalase dan perantara</strong>. Kami <strong>bukan</strong> pihak penjual, pembeli, maupun penyedia jasa pengantaran. Transaksi jual-beli, pembayaran, dan pengantaran sepenuhnya merupakan kesepakatan langsung antara Pemilik Toko dan Konsumen.</p>
            </div>

            <div>
                <h2 class="text-lg font-extrabold text-gray-900 mb-2">2. Kewajiban Pemilik Toko</h2>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Memberikan informasi toko, menu, harga, dan jam operasional yang <strong>akurat dan terbaru</strong>.</li>
                    <li>Bertanggung jawab penuh atas kualitas produk, kebersihan, dan halal/tidaknya makanan yang disajikan.</li>
                    <li>Menangani pesanan dan komunikasi lanjutan (via WhatsApp) dengan sigap dan ramah.</li>
                    <li>Menyelesaikan pengantaran (jika memilih metode delivery) secara mandiri atau menggunakan jasa pihak ketiga yang dipercaya.</li>
                </ul>
            </div>

            <div>
                <h2 class="text-lg font-extrabold text-gray-900 mb-2">3. Kewajiban Konsumen</h2>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Memberikan detail pesanan (nomor meja, alamat, nomor HP) yang benar saat checkout.</li>
                    <li>Melakukan konfirmasi dan pembayaran sesuai kesepakatan dengan Pemilik Toko.</li>
                    <li>Bersikap sopan dan tidak melakukan pemesanan fiktif (spam) yang merugikan Pemilik Toko.</li>
                </ul>
            </div>

            <div>
                <h2 class="text-lg font-extrabold text-gray-900 mb-2">4. Larangan Konten & Produk</h2>
                <p>Dilarang keras mengunggah atau menjual produk yang bersifat ilegal, berbahaya, melanggar hak cipta, mengandung SARA, atau melanggar hukum yang berlaku di Indonesia. LapakDA berhak menghapus produk dan menonaktifkan toko secara permanen jika terbukti melanggar aturan ini.</p>
            </div>

            <div>
                <h2 class="text-lg font-extrabold text-gray-900 mb-2">5. Perubahan Ketentuan</h2>
                <p>LapakDA dapat memperbarui Syarat & Ketentuan ini kapan saja. Pengguna disarankan untuk meninjau halaman ini secara berkala. Penggunaan layanan setelah pembaruan menandakan persetujuan terhadap ketentuan yang baru.</p>
            </div>

            <div class="pt-6 border-t border-gray-100">
                <h2 class="text-lg font-extrabold text-gray-900 mb-2">6. Hubungi Kami</h2>
                <p>Jika kamu memiliki pertanyaan terkait Syarat & Ketentuan ini, silakan hubungi tim LapakDA di <span class="font-bold text-[#B9232D]">[Email/WA Kontak Resmi]</span>.</p>
            </div>
            
            <div class="text-center text-xs text-gray-400 pt-6">
                Terakhir diperbarui: {{ date('d F Y') }}
            </div>
        </div>
    </main>

</body>
</html>