/**
 * PWA Service Worker - TeamCore
 * Minimal version - Network first, minimal caching
 */

var CACHE_NAME = 'teamcore-v1';

console.log('[SW] Service Worker script loaded');

/**
 * INSTALL - Do nothing, just skip waiting
 */
self.addEventListener('install', function(event) {
  console.log('[SW] Installing');
  self.skipWaiting();
});

/**
 * ACTIVATE - Claim clients immediately
 */
self.addEventListener('activate', function(event) {
  console.log('[SW] Activating');
  self.clients.claim();
});

/**
 * FETCH - Network first, no caching
 */
self.addEventListener('fetch', function(event) {
  if (event.request.method !== 'GET') {
    return;
  }
  
  // Try network
  event.respondWith(
    fetch(event.request)
      .then(function(response) {
        // Always return network response
        return response;
      })
      .catch(function(e) {
        // Network failed
        console.log('[SW] Network failed for ' + event.request.url);
        
        // Try to return offline page for HTML requests
        if (event.request.headers.get('accept') && event.request.headers.get('accept').indexOf('text/html') !== -1) {
          return fetch(new Request('/offline'));
        }
        
        // Return error for other requests
        return new Response('Offline', { status: 503 });
      })
  );
});

console.log('[SW] Ready');
