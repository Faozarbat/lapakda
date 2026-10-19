@php
    $ogTitle = $ogTitle ?? 'LapakDA — Katalog Online UMKM';
    $ogDescription = $ogDescription ?? 'Katalog online gratis untuk Resto & Toko UMKM. Lihat menu, pilih, dan pesan langsung via WhatsApp.';
    $ogImage = $ogImage ?? asset('og-default.png');
@endphp
<meta name="description" content="{{ $ogDescription }}">

{{-- Open Graph: dipakai WhatsApp, Facebook, Telegram, dll untuk bikin kartu preview saat link dibagikan --}}
<meta property="og:type" content="website">
<meta property="og:site_name" content="LapakDA">
<meta property="og:locale" content="id_ID">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDescription }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:url" content="{{ url()->current() }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $ogTitle }}">
<meta name="twitter:description" content="{{ $ogDescription }}">
<meta name="twitter:image" content="{{ $ogImage }}">