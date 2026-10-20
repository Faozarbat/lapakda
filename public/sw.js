const CACHE_NAME = 'lapakda-shell-v1';

const STATIC_ASSETS = [
  '/manifest.json',
  '/icons/icon-192.png',
  '/icons/icon-512.png',
  '/offline.html',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(STATIC_ASSETS))
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key)))
    )
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  const { request } = event;

  if (request.method !== 'GET') return;

  // Navigasi halaman (buka URL/klik link) -> SELALU coba network dulu,
  // baru fallback ke halaman offline kalau gagal total. Ini SENGAJA tidak
  // menyajikan versi cache untuk halaman, supaya konsumen tidak pernah
  // melihat harga/menu/status order yang basi (lihat PRODUCT_SPEC.md
  // checklist keamanan bagian PWA-specific).
  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request).catch(() => caches.match('/offline.html'))
    );
    return;
  }

  // Asset statis (icon, manifest, file build Vite yang sudah versioned by hash)
  // -> aman di-cache-first karena isinya tidak berubah tanpa nama file berubah juga.
  const url = new URL(request.url);
  if (STATIC_ASSETS.includes(url.pathname) || url.pathname.includes('/build/')) {
    event.respondWith(
      caches.match(request).then((cached) => cached || fetch(request))
    );
  }
});

// --- PUSH NOTIFICATION LISTENER ---
self.addEventListener('push', function(event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    const data = event.data?.json() ?? {};
    const title = data.title || "Pesanan Baru!";
    const options = {
        body: data.body || "Ada pesanan masuk ke toko Anda.",
        icon: "/icons/icon-192.png",
        badge: "/icons/icon-192.png",
        vibrate: [200, 100, 200, 100, 200, 100, 200], // Pola getar (seperti Grab/Shopee)
        data: {
            url: data.url || "/dashboard" // URL yang dibuka saat notif diklik
        }
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Aksi saat notifikasi diklik
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data.url)
    );
});