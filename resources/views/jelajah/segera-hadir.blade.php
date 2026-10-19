<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    @include('partials.pwa-head')
    @include('partials.og-meta', [
        'ogTitle' => $kategori['label'] . ' — Segera Hadir di LapakDA',
    ])
    <title>{{ $kategori['label'] }} — Segera Hadir | LapakDA</title>
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
        .card .icon { font-size: 3rem; margin-bottom: 12px; }
        .card h1 { font-size: 1.15rem; margin-bottom: 8px; }
        .card p { font-size: 0.85rem; color: #777; margin-bottom: 20px; }
        a.home-link {
            display: inline-block; background: #d32f2f; color: #fff; text-decoration: none;
            padding: 11px 22px; border-radius: 999px; font-size: 0.85rem; font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">{{ $kategori['icon'] }}</div>
        <h1>{{ $kategori['label'] }} Segera Hadir</h1>
        <p>Kategori ini masih kami siapkan. Nantikan ya — sementara ini kamu bisa jelajahi kategori yang sudah tersedia.</p>
        <a href="{{ route('home') }}" class="home-link">← Lihat Kategori Lain</a>
    </div>
</body>
</html>