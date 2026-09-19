// KeySaku Service Worker — v1.0.0
const CACHE_NAME   = 'keysaku-v1';
const STATIC_CACHE = 'keysaku-static-v1';

// Assets to pre-cache on install
const PRECACHE_URLS = [
    '/',
    '/offline',
];

// ── Install ──────────────────────────────────────────────────────────────────
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) =>
            cache.addAll(PRECACHE_URLS).catch(() => {})
        )
    );
    self.skipWaiting();
});

// ── Activate ─────────────────────────────────────────────────────────────────
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((k) => k !== CACHE_NAME && k !== STATIC_CACHE)
                    .map((k) => caches.delete(k))
            )
        )
    );
    self.clients.claim();
});

// ── Fetch — Network-first, fallback to cache ──────────────────────────────────
self.addEventListener('fetch', (event) => {
    // Only handle GET requests
    if (event.request.method !== 'GET') return;

    // Skip non-http(s) requests
    if (!event.request.url.startsWith('http')) return;

    // Skip Livewire / API / admin requests
    const url = new URL(event.request.url);
    if (
        url.pathname.startsWith('/livewire') ||
        url.pathname.startsWith('/api')
    ) {
        return;
    }

    // Static assets → Cache-first
    if (
        url.pathname.startsWith('/build/') ||
        url.pathname.match(/\.(css|js|png|jpg|jpeg|svg|ico|woff2?)$/)
    ) {
        event.respondWith(
            caches.match(event.request).then(
                (cached) =>
                    cached ||
                    fetch(event.request).then((res) => {
                        if (res.ok) {
                            const clone = res.clone();
                            caches.open(STATIC_CACHE).then((c) => c.put(event.request, clone));
                        }
                        return res;
                    })
            )
        );
        return;
    }

    // HTML pages → Network-first, offline fallback
    event.respondWith(
        fetch(event.request)
            .then((res) => {
                if (res.ok) {
                    const clone = res.clone();
                    caches.open(CACHE_NAME).then((c) => c.put(event.request, clone));
                }
                return res;
            })
            .catch(() =>
                caches.match(event.request).then(
                    (cached) => cached || caches.match('/')
                )
            )
    );
});
