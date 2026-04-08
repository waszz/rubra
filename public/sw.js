const CACHE_NAME = 'rubra-v1';

// Recursos que se cachean al instalar
const PRECACHE_URLS = [
    '/',
    '/dashboard',
    '/images/logo.png',
    '/offline.html',
];

// ── INSTALL ──────────────────────────────────────────────────
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_URLS).catch(() => {
                // Si algún recurso falla, continuar igualmente
            });
        }).then(() => self.skipWaiting())
    );
});

// ── ACTIVATE ─────────────────────────────────────────────────
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME)
                    .map((name) => caches.delete(name))
            );
        }).then(() => self.clients.claim())
    );
});

// ── FETCH ─────────────────────────────────────────────────────
// Estrategia: Network First (app dinámica), fallback a cache, sino offline.html
self.addEventListener('fetch', (event) => {
    // Solo interceptar peticiones GET del mismo origen
    if (event.request.method !== 'GET') return;
    if (!event.request.url.startsWith(self.location.origin)) return;

    // No interceptar llamadas Livewire/Ajax
    const url = new URL(event.request.url);
    if (url.pathname.startsWith('/livewire')) return;

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                // Guardar respuesta exitosa en cache
                if (response && response.status === 200) {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseClone);
                    });
                }
                return response;
            })
            .catch(() => {
                // Sin red: intentar desde cache
                return caches.match(event.request).then((cached) => {
                    return cached || caches.match('/offline.html');
                });
            })
    );
});
