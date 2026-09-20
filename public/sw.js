// KeySaku Service Worker — v1.1.0
const CACHE_NAME   = 'keysaku-v1';
const STATIC_CACHE = 'keysaku-static-v1';

// ── Install ──────────────────────────────────────────────────────────────────
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) =>
            // Only pre-cache the login page as offline shell
            // NEVER pre-cache '/' as it redirects to authenticated dashboard
            cache.addAll(['/login']).catch(() => {})
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

// ── Fetch — Network-first, no authenticated HTML cached ───────────────────────
self.addEventListener('fetch', (event) => {
    // Only handle GET requests
    if (event.request.method !== 'GET') return;

    // Skip non-http(s) requests
    if (!event.request.url.startsWith('http')) return;

    const url = new URL(event.request.url);

    // Skip Livewire / API requests — always bypass SW
    if (
        url.pathname.startsWith('/livewire') ||
        url.pathname.startsWith('/api')
    ) {
        return;
    }

    // Static assets → Cache-first (safe; no user data in CSS/JS/images)
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

    // HTML pages — SECURITY: Never cache authenticated pages.
    // Network-first: if offline, fall back to /login (public page only).
    // Intentionally do NOT write HTML responses to cache.
    event.respondWith(
        fetch(event.request)
            .catch(() =>
                caches.match('/login').then(
                    (cached) => cached || Response.error()
                )
            )
    );
});
