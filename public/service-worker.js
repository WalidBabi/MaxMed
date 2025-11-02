self.addEventListener('install', (event) => {
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(self.clients.claim());
});

self.addEventListener('push', (event) => {
  let data = {};
  try {
    data = event.data ? event.data.json() : {};
  } catch (e) {
    data = { title: 'MaxMed', body: event.data ? event.data.text() : '' };
  }

  const title = data.title || 'MaxMed';
  const options = {
    body: data.body || '',
    icon: '/img/favicon/android-chrome-192x192.png',
    badge: '/img/favicon/android-chrome-192x192.png',
    data: { url: data.url || '/' },
  };

  event.waitUntil(self.registration.showNotification(title, options));
  // Also ping server so we can verify delivery on backend logs
  event.waitUntil((async () => {
    try {
      const sub = await self.registration.pushManager.getSubscription();
      const payload = { title, receivedAt: Date.now(), endpoint: sub ? sub.endpoint : null };
      await fetch('/push/received', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
    } catch (e) {}
  })());
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  const targetUrl = (event.notification && event.notification.data && event.notification.data.url) || '/';

  event.waitUntil(
    (async () => {
      const allClients = await clients.matchAll({ type: 'window', includeUncontrolled: true });
      for (const client of allClients) {
        try {
          const url = new URL(client.url);
          if (url.pathname === targetUrl) {
            return client.focus();
          }
        } catch (e) {}
      }
      return clients.openWindow(targetUrl);
    })()
  );
});


