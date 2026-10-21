<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Kesalahan Server — LapakDA</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-[#F8F9FA] text-gray-800 antialiased h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl p-8 border border-gray-100 shadow-xl text-center">
        <div class="w-24 h-24 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">500</h1>
        <h2 class="text-lg font-bold text-gray-700 mb-2">Terjadi Kesalahan Sistem</h2>
        <p class="text-sm text-gray-500 mb-8">Maaf, server kami sedang mengalami kendala teknis. Tim kami telah diberitahu dan sedang memperbaikinya.</p>
        <a href="{{ url('/') }}" class="inline-flex items-center justify-center w-full bg-[#B9232D] text-white font-bold py-3.5 rounded-xl shadow-lg shadow-red-500/30 hover:bg-red-800 transition-colors">
            Muat Ulang Beranda
        </a>
    </div>
</body>
</html>