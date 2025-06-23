self.addEventListener('push', function(event) {
    const data = event.data.json();

    const title = data.title || 'Notifikasi Baru!';
    const options = {
        body: data.body || 'Ada pesan baru untukmu.',
        icon: '/icon-192x192.png', // sesuaikan dengan icon kamu
        badge: '/icon-192x192.png', // opsional
        data: {
            url: data.url || '/'
        }
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
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
