<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#B9232D">
    <title>Kebijakan Privasi — LapakDA</title>
    @include('partials.pwa-head')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F9FA] text-gray-800 antialiased pb-10">
    <header class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-gray-100">
        <div class="max-w-3xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ url()->previous() }}" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-700 hover:bg-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
            </a>
            <h1 class="font-bold text-base text-gray-900 absolute left-1/2 -translate-x-1/2">Kebijakan Privasi</h1>
            <div class="w-10"></div>
        </div>
    </header>
    <main class="max-w-3xl mx-auto px-4 pt-6 pb-20">
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-6 text-sm leading-relaxed text-gray-600">
            <div>
                <h2 class="text-lg font-extrabold text-gray-900 mb-2">1. Data yang Dikumpulkan</h2>
                <p>Kami mengumpulkan informasi yang kamu berikan secara langsung, seperti <strong>Nama</strong> dan <strong>Nomor WhatsApp</strong>. Saat melakukan checkout, kami juga dapat mengakses <strong>titik koordinat lokasi</strong> (jika kamu mengizinkan) untuk keperluan pengantaran pesanan.</p>
            </div>
            <div>
                <h2 class="text-lg font-extrabold text-gray-900 mb-2">2. Penggunaan Data</h2>
                <p>Data kamu digunakan untuk:</p>
                <ul class="list-disc pl-5 space-y-1 mt-2">
                    <li>Memproses dan mengirimkan pesananmu ke Pemilik Toko.</li>
                    <li>Menyimpan riwayat pesanan agar kamu bisa melihatnya kembali.</li>
                    <li>Mengirimkan informasi promo atau pembaruan layanan (jika disetujui).</li>
                </ul>
            </div>
            <div>
                <h2 class="text-lg font-extrabold text-gray-900 mb-2">3. Berbagi Data ke Pihak Ketiga</h2>
                <p>Kami tidak menjual datamu. Kami hanya membagikan datamu kepada <strong>Pemilik Toko</strong> (terkait pesanan) dan menggunakan pihak ketiga untuk operasional (seperti WhatsApp untuk pengiriman pesan konfirmasi).</p>
            </div>
            <div>
                <h2 class="text-lg font-extrabold text-gray-900 mb-2">4. Penyimpanan Data Lokasi</h2>
                <p>Titik lokasi yang kamu simpan di profil hanya disimpan sebagai alamat terakhir untuk mempermudah checkout berikutnya. Kamu dapat mengubahnya kapan saja.</p>
            </div>
            <div>
                <h2 class="text-lg font-extrabold text-gray-900 mb-2">5. Hak Kamu</h2>
                <p>Kamu berhak untuk mengubah data profilmu, atau meminta penghapusan akun beserta data riwayat pesananmu kapan saja melalui menu Pengaturan Profil.</p>
            </div>
            <div class="pt-6 border-t border-gray-100">
                <h2 class="text-lg font-extrabold text-gray-900 mb-2">6. Hubungi Kami</h2>
                <p>Jika ada pertanyaan mengenai privasi, hubungi kami di <span class="font-bold text-[#B9232D]">[Email/WA Kontak Resmi]</span>.</p>
            </div>
        </div>
    </main>
</body>
</html>