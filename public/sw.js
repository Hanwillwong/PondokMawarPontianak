const CACHE_NAME = "offline-v1";

const filesToCache = [
    '/',                // Beranda
    '/offline.html',    // Halaman fallback
    '/css/app.css',     // Tambahkan CSS
    '/js/app.js',       // Tambahkan JS utama
    '/logo.png',        // Tambahkan logo PWA
];

self.addEventListener("install", function (event) {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(filesToCache);
        })
    );
});

self.addEventListener("fetch", function (event) {
    // Hanya intercept request HTTP
    if (event.request.url.startsWith('http')) {
        event.respondWith(
            fetch(event.request)
                .then(response => {
                    // Cache setiap request yang berhasil
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(event.request, responseClone);
                    });
                    return response;
                })
                .catch(() => {
                    // Kalau gagal (misal offline), ambil dari cache
                    return caches.match(event.request).then(res => {
                        return res || caches.match('/offline.html');
                    });
                })
        );
    }
});

self.addEventListener('push', function(event) {
    let data = {};
    try {
        data = event.data.json();
    } catch (e) {
        data = {
            title: 'Notifikasi',
            body: 'Ada notifikasi baru.',
            url: '/',
            requireInteraction: false
        };
    }

    const options = {
        body: data.body,
        icon: '/logo.png', // sesuaikan dengan ikon kamu
        badge: '/logo.png',
        data: {
            url: data.url
        },
        requireInteraction: data.requireInteraction || false
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
            for (const client of clientList) {
                if (client.url === event.notification.data.url && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(event.notification.data.url);
            }
        })
    );
});
