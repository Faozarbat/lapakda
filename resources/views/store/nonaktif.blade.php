<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    @include('partials.pwa-head')
    <title>{{ $store->nama }} — Sedang Tidak Aktif</title>
    <style>
        :root { box-sizing: border-box; padding-top: env(safe-area-inset-top, 0px); }
        *, *::before, *::after { box-sizing: inherit; margin: 0; padding: 0; }
        html { font-size: 16px; }
        body {
            font-family: -apple-system, "Segoe UI", Roboto, sans-serif;
            background: #f5f5f5; color: #1a1a1a;
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            padding: 24px;
        }
        .card {
            background: #fff; border-radius: 16px; padding: 40px 28px;
            text-align: center; max-width: 340px; box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        }
        .card svg { width: 56px; height: 56px; margin-bottom: 16px; color: #d32f2f; }
        .card h1 { font-size: 1.1rem; margin-bottom: 8px; }
        .card p { font-size: 0.85rem; color: #777; }
        .card .nama-toko { font-weight: 700; color: #1a1a1a; }
        a.home-link { display: inline-block; margin-top: 20px; font-size: 0.85rem; color: #d32f2f; font-weight: 600; text-decoration: none; }
    </style>
</head>
<body>
    <div class="card">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M12 8v4"></path>
            <path d="M12 16h.01"></path>
        </svg>
        <h1><span class="nama-toko">{{ $store->nama }}</span> sedang tidak aktif</h1>
        <p>Toko ini untuk sementara tidak menerima pesanan. Coba kunjungi lagi lain waktu ya.</p>
        <a href="{{ route('home') }}" class="home-link">← Lihat toko lain di LapakDA</a>
    </div>
</body>
</html>