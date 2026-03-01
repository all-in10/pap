/**
 * PWA Service Worker - TeamCore
 * 
 * Estratégia: Network-First
 * - Tenta sempre usar a rede para conteúdo fresco
 * - Se rede falhar, usa o cache como fallback
 * - Ideal para apps com dados dinâmicos (não para offline-first games)
 */

const CACHE_VERSION = 'v1';
const CACHE_NAME = `teamcore-${CACHE_VERSION}`;
const OFFLINE_URL = '/offline';

// URLs que sempre devem estar cacheadas (assets estáticos)
const STATIC_ASSETS = [
  '/',
  '/offline',
  '/css/app.css',
  '/js/app.js',
];

// Padrões de URLs que NUNCA devem ser cacheadas
const NO_CACHE_PATTERNS = [
  /\/admin\/.*\/delete/i,          // Nunca cache DELETE operations
  /\/logout/i,                      // Nunca cache logout
  /\.php$/i,                        // Nunca cache PHP direto
];

// Padrões que podem ficar offline (GET requests)
const OFFLINE_CACHEABLE_PATTERNS = [
  /\/api\/employees/i,
  /\/api\/contracts/i,
  /\/api\/dashboard/i,
  /\/dashboard/i,
  /\.js$/i,
  /\.css$/i,
  /\.png$/i,
  /\.jpg$/i,
  /\.jpeg$/i,
  /\.svg$/i,
  /\.gif$/i,
  /\.webp$/i,
  /\.woff2?$/i,
  /\.ttf$/i,
];

/**
 * INSTALL: Cache static assets
 */
self.addEventListener('install', (event) => {
  console.log('[SW] Installing service worker...');

  event.waitUntil(
    caches
      .open(CACHE_NAME)
      .then((cache) => {
        console.log(`[SW] Caching static assets in ${CACHE_NAME}`);
        return cache.addAll(STATIC_ASSETS).catch((error) => {
          console.warn('[SW] Failed to cache some static assets:', error);
          // Continue even if some assets fail to cache
          return Promise.resolve();
        });
      })
      .then(() => self.skipWaiting()) // Activate immediately
      .catch((error) => {
        console.error('[SW] Installation failed:', error);
      })
  );
});

/**
 * ACTIVATE: Clean old cache versions
 */
self.addEventListener('activate', (event) => {
  console.log('[SW] Activating service worker...');

  event.waitUntil(
    caches
      .keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => {
            if (cacheName !== CACHE_NAME) {
              console.log(`[SW] Deleting old cache: ${cacheName}`);
              return caches.delete(cacheName);
            }
          })
        );
      })
      .then(() => self.clients.claim()) // Take control immediately
      .catch((error) => {
        console.error('[SW] Activation failed:', error);
      })
  );
});

/**
 * Helper: Check if URL should be cached
 */
function shouldCache(url) {
  try {
    const urlObj = new URL(url);
    const pathname = urlObj.pathname;

    // Nunca cache certos padrões
    if (NO_CACHE_PATTERNS.some((pattern) => pattern.test(pathname))) {
      return false;
    }

    // Cache GET requests que match offline patterns
    if (OFFLINE_CACHEABLE_PATTERNS.some((pattern) => pattern.test(pathname))) {
      return true;
    }

    return true; // Cache por padrão (GET requests simples)
  } catch (error) {
    console.error('[SW] Error checking cache eligibility:', error);
    return false;
  }
}

/**
 * Helper: Check if should try network
 */
function shouldTryNetwork(request) {
  // Nunca network para alguns padrões
  if (NO_CACHE_PATTERNS.some((pattern) => pattern.test(request.url))) {
    return true;
  }

  // Network-first para tudo
  return true;
}

/**
 * FETCH: Network-first strategy
 */
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const { method, url } = request;

  // Ignorar non-GET requests (POST, DELETE, PUT, etc apenas em network)
  if (method !== 'GET') {
    return; // Vai para network por padrão
  }

  // Ignorar URLs de navegação cruzada (cross-origin)
  try {
    const urlObj = new URL(url);
    if (
      urlObj.origin !== self.location.origin &&
      !url.includes('cdn.') &&
      !url.includes('fonts.googleapis.com')
    ) {
      return;
    }
  } catch (error) {
    console.error('[SW] Invalid URL:', url);
    return;
  }

  // Network-first strategy
  event.respondWith(networkFirstStrategy(request));
});

/**
 * Network-first strategy implementation
 */
async function networkFirstStrategy(request) {
  const canCache = shouldCache(request.url);
  const cacheKey = createCacheKey(request.url);

  try {
    // 1. Try network first
    const networkResponse = await fetchWithTimeout(request, 8000); // 8s timeout

    // Clone response para cache + resposta
    if (canCache && networkResponse.ok) {
      const responseToCache = networkResponse.clone();
      caches.open(CACHE_NAME).then((cache) => {
        cache.put(cacheKey, responseToCache).catch((error) => {
          console.warn('[SW] Failed to cache response:', error);
        });
      });
    }

    return networkResponse;
  } catch (networkError) {
    console.log('[SW] Network failed for:', request.url, networkError.message);

    // 2. Network failed, try cache
    try {
      const cachedResponse = canCache
        ? await caches.match(cacheKey)
        : await caches.match(request.url);

      if (cachedResponse) {
        console.log('[SW] Serving from cache:', request.url);
        return cachedResponse;
      }
    } catch (cacheError) {
      console.error('[SW] Cache lookup failed:', cacheError);
    }

    // 3. Both network and cache failed
    if (request.headers.get('accept')?.includes('text/html')) {
      // Se é página HTML, retorna offline page
      console.log('[SW] Returning offline page for:', request.url);
      try {
        return await caches.match(OFFLINE_URL);
      } catch (error) {
        console.error('[SW] Failed to get offline page:', error);
        return new Response('Offline - please check your connection', {
          status: 503,
          statusText: 'Service Unavailable',
          headers: { 'Content-Type': 'text/plain' },
        });
      }
    }

    // Para requisições não-HTML que falharam, retorna erro genérico
    return new Response('Request failed', {
      status: 503,
      statusText: 'Service Unavailable',
      headers: { 'Content-Type': 'text/plain' },
    });
  }
}

/**
 * Fetch com timeout
 */
function fetchWithTimeout(request, timeout = 8000) {
  return Promise.race([
    fetch(request),
    new Promise((_, reject) =>
      setTimeout(() => reject(new Error('Fetch timeout')), timeout)
    ),
  ]);
}

/**
 * Create consistent cache key
 */
function createCacheKey(url) {
  try {
    const urlObj = new URL(url);
    // Remove certos query params que podem variar
    urlObj.searchParams.delete('_');
    urlObj.searchParams.delete('utm_*');
    return new Request(urlObj.toString(), { method: 'GET' });
  } catch {
    return request;
  }
}

/**
 * Background Sync for failed requests (optional)
 */
self.addEventListener('sync', (event) => {
  console.log('[SW] Background sync event:', event.tag);

  if (event.tag === 'sync-failed-requests') {
    event.waitUntil(syncFailedRequests());
  }
});

async function syncFailedRequests() {
  try {
    const db = await openDB();
    const failedRequests = await getAllFailedRequests(db);

    for (const request of failedRequests) {
      try {
        const response = await fetch(request.url, {
          method: request.method,
          headers: request.headers,
          body: request.body,
        });

        if (response.ok) {
          await removeFailedRequest(db, request.id);
          // Notify clients about successful sync
          const clients = await self.clients.matchAll();
          clients.forEach((client) => {
            client.postMessage({
              type: 'SYNC_SUCCESS',
              data: request,
            });
          });
        }
      } catch (error) {
        console.error('[SW] Failed to sync request:', error);
      }
    }
  } catch (error) {
    console.error('[SW] Background sync error:', error);
  }
}

/**
 * Message from clients
 */
self.addEventListener('message', (event) => {
  const { type, data } = event.data;

  console.log('[SW] Message received:', type);

  switch (type) {
    case 'SKIP_WAITING':
      self.skipWaiting();
      break;

    case 'CLEAR_CACHE':
      caches
        .delete(CACHE_NAME)
        .then(() => console.log('[SW] Cache cleared'))
        .catch((error) => console.error('[SW] Failed to clear cache:', error));
      break;

    case 'STORE_FAILED_REQUEST':
      storeFailedRequest(data)
        .then(() => {
          event.ports[0].postMessage({ success: true });
        })
        .catch((error) => {
          event.ports[0].postMessage({ success: false, error: error.message });
        });
      break;

    default:
      console.log('[SW] Unknown message type:', type);
  }
});

/**
 * Push notifications
 */
self.addEventListener('push', (event) => {
  console.log('[SW] Push notification received');

  let notificationData = {
    title: 'TeamCore Notification',
    body: 'Nova notificação recebida',
    icon: '/pwa-icons/icon-192x192.png',
    badge: '/pwa-icons/icon-192x192.png',
    tag: 'teamcore-notification',
    requireInteraction: false,
  };

  if (event.data) {
    try {
      const payload = event.data.json();
      notificationData = { ...notificationData, ...payload };
    } catch (error) {
      notificationData.body = event.data.text();
    }
  }

  event.waitUntil(
    self.registration.showNotification(notificationData.title, {
      body: notificationData.body,
      icon: notificationData.icon,
      badge: notificationData.badge,
      tag: notificationData.tag,
      requireInteraction: notificationData.requireInteraction,
      data: notificationData.data || {},
      actions: [
        {
          action: 'open',
          title: 'Abrir',
          icon: '/pwa-icons/icon-192x192.png',
        },
        {
          action: 'close',
          title: 'Fechar',
        },
      ],
    })
  );
});

/**
 * Notification click handler
 */
self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  event.waitUntil(
    self.clients.matchAll({ type: 'window' }).then((clientList) => {
      // Se abrir já existe, foca nela
      for (const client of clientList) {
        if ('focus' in client) {
          client.focus();
          return;
        }
      }

      // Senão, abre uma nova
      if (self.clients.openWindow) {
        const url = event.notification.data?.url || '/dashboard';
        return self.clients.openWindow(url);
      }
    })
  );
});

self.addEventListener('notificationclose', (event) => {
  console.log('[SW] Notification closed:', event.notification.tag);
});

console.log('[SW] Service Worker loaded');
