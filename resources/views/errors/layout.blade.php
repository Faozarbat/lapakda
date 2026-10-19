<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title') — LapakDA</title>
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
            text-align: center; max-width: 340px; width: 100%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        }
        .card svg { width: 56px; height: 56px; margin-bottom: 14px; color: #d32f2f; }
        .card .kode { font-size: 0.75rem; color: #aaa; font-weight: 700; letter-spacing: 1px; margin-bottom: 6px; }
        .card h1 { font-size: 1.1rem; margin-bottom: 8px; }
        .card p { font-size: 0.85rem; color: #777; }
        .card .aksi { margin-top: 22px; display: flex; flex-direction: column; gap: 10px; }
        .card .btn {
            display: block; padding: 12px; border-radius: 8px; font-size: 0.85rem;
            font-weight: 700; text-decoration: none; border: none; cursor: pointer; font-family: inherit;
        }
        .card .btn-utama { background: #d32f2f; color: #fff; }
        .card .btn-sekunder { background: #fff; color: #d32f2f; border: 1px solid #d32f2f; }
    </style>
</head>
<body>
    <div class="card">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M12 8v4"></path>
            <path d="M12 16h.01"></path>
        </svg>
        <div class="kode">ERROR @yield('kode')</div>
        <h1>@yield('title')</h1>
        <p>@yield('message')</p>

        <div class="aksi">
            @yield('aksi')
            <a href="{{ url('/') }}" class="btn btn-sekunder">← Kembali ke LapakDA</a>
        </div>
    </div>
</body>
</html>
