// Selly Laundry service worker — app shell caching.
// Relative URLs resolve against the SW scope, so this works in a sub-folder too.
const CACHE = 'selly-v4';
const APP_SHELL = ['offline.html', 'icons/icon-192.png', 'manifest.webmanifest'];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE).then((cache) => cache.addAll(APP_SHELL)).catch(() => {})
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Only handle GET navigations and static assets; let POST/Livewire pass through.
    if (request.method !== 'GET') return;

    const url = new URL(request.url);

    // Network-first for documents (HTML), fall back to cache/offline page.
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((res) => {
                    const copy = res.clone();
                    caches.open(CACHE).then((c) => c.put(request, copy));
                    return res;
                })
                .catch(() => caches.match(request).then((r) => r || caches.match('offline.html')))
        );
        return;
    }

    // Cache-first for same-origin static assets.
    if (url.origin === self.location.origin && /\.(css|js|png|jpg|jpeg|svg|woff2?|ico)$/.test(url.pathname)) {
        event.respondWith(
            caches.match(request).then((cached) =>
                cached ||
                fetch(request).then((res) => {
                    const copy = res.clone();
                    caches.open(CACHE).then((c) => c.put(request, copy));
                    return res;
                }).catch(() => cached)
            )
        );
    }
});

// Show received Web Push notifications.
self.addEventListener('push', (event) => {
    let data = { title: 'Selly Laundry', body: 'Ada pembaruan pesanan.' };
    try { data = event.data.json(); } catch (e) {}
    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: '/icons/icon-192.png',
            badge: '/icons/icon-192.png',
        })
    );
});
