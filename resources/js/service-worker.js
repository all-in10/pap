/**
 * PWA Service Worker - TeamCore
 * Simple, compatible version
 */

var CACHE_VERSION = 'v1';
var CACHE_NAME = 'teamcore-' + CACHE_VERSION;
var OFFLINE_URL = '/offline';

console.log('[SW] Service Worker script loaded');

/**
 * INSTALL
 */
self.addEventListener('install', function(event) {
  console.log('[SW] Installing...');
  
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(function(cache) {
        console.log('[SW] Cache opened: ' + CACHE_NAME);
        return cache.add('/offline').catch(function(e) {
          console.warn('[SW] Could not cache offline page:', e);
        });
      })
      .then(function() {
        console.log('[SW] Installation complete');
        return self.skipWaiting();
      })
  );
});

/**
 * ACTIVATE
 */
self.addEventListener('activate', function(event) {
  console.log('[SW] Activating...');
  
  event.waitUntil(
    caches.keys().then(function(cacheNames) {
      return Promise.all(
        cacheNames.map(function(cacheName) {
          if (cacheName !== CACHE_NAME) {
            console.log('[SW] Deleting old cache: ' + cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    }).then(function() {
      console.log('[SW] Activation complete');
      return self.clients.claim();
    })
  );
});

/**
 * FETCH - Network first
 */
self.addEventListener('fetch', function(event) {
  var request = event.request;
  
  // Only handle GET
  if (request.method !== 'GET') {
    return;
  }

  event.respondWith(
    fetch(request)
      .then(function(response) {
        if (!response || response.status !== 200) {
          return response;
        }
        
        // Cache successful responses
        var responseToCache = response.clone();
        caches.open(CACHE_NAME).then(function(cache) {
          cache.put(request, responseToCache);
        });
        
        return response;
      })
      .catch(function(error) {
        console.log('[SW] Fetch failed, trying cache');
        
        return caches.match(request)
          .then(function(response) {
            if (response) {
              return response;
            }
            
            // HTML requests fallback to offline page
            if (request.headers.get('accept') && request.headers.get('accept').indexOf('text/html') !== -1) {
              return caches.match(OFFLINE_URL);
            }
            
            return new Response('Offline', { status: 503 });
          });
      })
  );
});

/**
 * PUSH notifications
 */
self.addEventListener('push', function(event) {
  console.log('[SW] Push event');
  
  var notificationData = {
    title: 'TeamCore',
    body: 'Notificacao',
    icon: '/pwa-icons/icon-192x192.png'
  };
  
  if (event.data) {
    try {
      notificationData = event.data.json();
    } catch (e) {
      notificationData.body = event.data.text();
    }
  }
  
  event.waitUntil(
    self.registration.showNotification(notificationData.title, {
      body: notificationData.body,
      icon: notificationData.icon
    })
  );
});

/**
 * NOTIFICATION CLICK
 */
self.addEventListener('notificationclick', function(event) {
  event.notification.close();
  
  event.waitUntil(
    self.clients.matchAll({ type: 'window' }).then(function(clientList) {
      for (var i = 0; i < clientList.length; i++) {
        if (clientList[i].url === '/' && 'focus' in clientList[i]) {
          return clientList[i].focus();
        }
      }
      if (self.clients.openWindow) {
        return self.clients.openWindow('/');
      }
    })
  );
});

console.log('[SW] Service Worker ready');
