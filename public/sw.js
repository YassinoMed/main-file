/**
 * ERPGo SaaS - Service Worker for PWA
 * Enables offline caching, push notifications, and app-like experience
 */
const CACHE_NAME = 'erpgo-v2';
const OFFLINE_URL = '/offline';

const STATIC_ASSETS = [
    '/',
    '/dashboard',
    '/css/ux-enhancements.css',
    '/js/ux-enhancements.js',
    '/css/custom.css',
    '/css/custom-dark.css',
    '/js/custom.js',
    '/favicon.ico',
    '/manifest.json',
    '/offline'
];

// Install event - cache static assets
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            console.log('Caching static assets');
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
        )
    );
    self.clients.claim();
});

// Fetch event - network first, fallback to cache
self.addEventListener('fetch', event => {
    if (event.request.method !== 'GET') return;
    
    event.respondWith(
        fetch(event.request)
            .then(response => {
                const clone = response.clone();
                caches.open(CACHE_NAME).then(cache => {
                    // Cache CSS, JS, images, fonts
                    if (event.request.url.match(/\.(css|js|png|jpg|svg|woff|woff2|json)$/)) {
                        cache.put(event.request, clone);
                    }
                });
                return response;
            })
            .catch(() => {
                // Return cached version if offline
                return caches.match(event.request).then(cachedResponse => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    // Return offline page for navigation requests
                    if (event.request.mode === 'navigate') {
                        return caches.match(OFFLINE_URL);
                    }
                    return new Response('Offline', { status: 503 });
                });
            })
    );
});

// Push notification event
self.addEventListener('push', event => {
    const data = event.data ? event.data.json() : {};
    const title = data.title || 'ERPGo Notification';
    const options = {
        body: data.body || 'You have a new notification',
        icon: '/favicon.ico',
        badge: '/favicon.ico',
        vibrate: [100, 50, 100],
        data: {
            url: data.url || '/dashboard'
        },
        actions: [
            { action: 'view', title: 'View' },
            { action: 'dismiss', title: 'Dismiss' }
        ]
    };
    
    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Notification click event
self.addEventListener('notificationclick', event => {
    event.notification.close();
    
    if (event.action === 'dismiss') return;
    
    const url = event.notification.data.url || '/dashboard';
    
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(clientList => {
            // Focus existing window if available
            for (const client of clientList) {
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    client.navigate(url);
                    return client.focus();
                }
            }
            // Open new window
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});

// Background sync for offline actions
self.addEventListener('sync', event => {
    if (event.tag === 'sync-pending-actions') {
        event.waitUntil(syncPendingActions());
    }
});

async function syncPendingActions() {
    const cache = await caches.open(CACHE_NAME);
    const requests = await cache.keys();
    
    for (const request of requests) {
        if (request.url.includes('/api/offline-queue')) {
            const response = await cache.match(request);
            if (response) {
                const data = await response.json();
                // Retry failed requests
                try {
                    await fetch(data.url, {
                        method: data.method || 'POST',
                        headers: data.headers || {},
                        body: JSON.stringify(data.body)
                    });
                    await cache.delete(request);
                } catch (e) {
                    console.log('Sync failed, will retry later');
                }
            }
        }
    }
}
