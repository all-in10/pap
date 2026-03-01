# PWA Quick Reference - TeamCore

Guia rápido para desenvolvimento e troubleshooting PWA.

## 🚀 Getting Started (5 min)

```bash
# 1. Gerar ícones
cd public/pwa-icons && php generate-icons.php && cd ../..

# 2. Build Vite (compila Service Worker)
npm run build

# 3. Migrations
php artisan migrate

# 4. Validar
php artisan pwa:validate

# 5. Rodar
php artisan serve &
php artisan queue:work
```

## 📱 Testing Checklist

### ✓ Desktop Chrome/Edge

- [ ] F12 → Application → Service Workers (deve estar "activated and running")
- [ ] F12 → Application → Cache Storage → teamcore-v1 (deve ter arquivos)
- [ ] F12 → Network → Offline ✓ → Refresh → Offline page aparece
- [ ] Menu (⋮) → "Create shortcut" (opção aparecer em 30s)

### ✓ Mobile (Android Chrome)

- [ ] Abrir app em Chrome
- [ ] Menu (⋮) → "Install app"
- [ ] App aparece na home screen
- [ ] Abrir app instalado
- [ ] Airplane mode ON
- [ ] Trocar de aba e voltar
- [ ] App ainda funciona (cached pages)

### ✓ Push Notifications

- [ ] Rodar: `php artisan notify:test-push --user=1`
- [ ] Checar: `php artisan queue:work` rodando em outro terminal
- [ ] Notificação aparece em 5 segundos

## 🔍 Debug Commands

```bash
# Validar PWA config
php artisan pwa:validate

# Enviar test push
php artisan notify:test-push --user=1

# Ver failed push jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Ver Service Worker logs no browser
# F12 → Console → Filter: "[SW]"
```

## 📊 File Checklist

```
✓ public/manifest.json
✓ public/js/sw.js (gerado pelo Vite)
✓ public/pwa-icons/icon-192x192.png
✓ public/pwa-icons/icon-512x512.png
✓ public/pwa-icons/maskable-icon-192x192.png
✓ resources/js/service-worker.js
✓ resources/views/offline.blade.php
✓ app/Http/Middleware/PWAHeadersMiddleware.php
✓ app/Http/Controllers/API/PushSubscriptionController.php
✓ app/Models/UserPushSubscription.php
✓ app/Jobs/SendPushNotification.php
✓ app/Console/Commands/SendTestPushNotification.php
✓ app/Console/Commands/ValidatePWAConfiguration.php
✓ bootstrap/app.php (middleware registrado)
✓ routes/api.php
✓ routes/web.php (rota /offline)
✓ vite.config.js (service-worker.js entry)
```

## 🔧 Common Issues & Fixes

| Issue | Fix |
|-------|-----|
| SW não registra | `npm run build` + hard refresh (Ctrl+Shift+Del) |
| Cache vazio | Unregister SW → DevTools → App → SW → Unregister |
| App não instala | Desktop: Menu → Install, Mobile: Wait 30s |
| Push não funciona | `php artisan queue:work` rodando? |
| Headers errados | Middleware registrado em bootstrap/app.php? |
| Offline 404 | Check: `/offline` rota existe em routes/web.php |

## 🌐 Browser DevTools (F12)

### Application Tab
- **Service Workers:** Status, update check, unregister
- **Cache Storage:** Ver arquivos em cache
- **Manifest:** Ver manifest.json details

### Network Tab
- **Filter:** `sw.js` → check Cache-Control headers
- **Filter:** `manifest.json` → check headers
- **Offline checkbox:** Simular offline mode

### Console Tab
- `[PWA]` logs: Registration, updates
- `[SW]` logs: Fetch events, caching
- Errors: Stack traces

## 📲 Install Detection (JavaScript)

```javascript
// Capturar beforeinstallprompt
let installPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  installPrompt = e;
  // Mostrar botão "Install"
});

// Disparar install
async function installApp() {
  if (installPrompt) {
    installPrompt.prompt();
    const { outcome } = await installPrompt.userChoice;
    console.log(`User response to the install prompt: ${outcome}`);
    installPrompt = null;
  }
}
```

## 📡 Push Subscription Flow

```javascript
// 1. Subscribe to notifications
async function subscribeToPush() {
  const registration = await navigator.serviceWorker.ready;
  
  const subscription = await registration.pushManager.subscribe({
    userVisibleOnly: true,
  });

  // 2. Send to backend
  const response = await fetch('/api/push-subscribe', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': getCsrfToken(),
    },
    body: JSON.stringify({
      endpoint: subscription.endpoint,
      public_key: uint8ArrayToBase64(subscription.getKey('p256dh')),
      auth_secret: uint8ArrayToBase64(subscription.getKey('auth')),
    }),
  });

  console.log('Subscription saved:', await response.json());
}

// 3. Backend sends notification
// php artisan notify:test-push --user=1

// 4. Service Worker receives and shows notification
self.addEventListener('push', (event) => {
  const data = event.data.json();
  self.registration.showNotification(data.title, data);
});
```

## 🚨 Offline Indicator Pattern

```html
<!-- Adicionar em app layout -->
<div id="offline-indicator" style="display: none; 
    background: #fee2e2; 
    color: #7f1d1d; 
    padding: 10px; 
    text-align: center;">
  📡 Você está offline
</div>

<script>
window.addEventListener('online', () => {
  document.getElementById('offline-indicator').style.display = 'none';
});
window.addEventListener('offline', () => {
  document.getElementById('offline-indicator').style.display = 'block';
});
</script>
```

## 📈 Performance Optimization

```javascript
// Preload critical resources
<link rel="preload" as="script" href="/js/app.js">
<link rel="preload" as="style" href="/css/app.css">

// Prefetch secondary resources
<link rel="prefetch" href="/images/logo.svg">

// DNS prefetch external
<link rel="dns-prefetch" href="//fonts.googleapis.com">
```

## 🔒 HTTPS/Production Requirements

```bash
# 1. Add to .env
APP_URL=https://yourdomain.com    # Must be HTTPS!

# 2. Nginx headers
add_header Service-Worker-Allowed "/";
add_header Strict-Transport-Security "max-age=31536000";

# 3. Build & migrate
npm run build
php artisan migrate --force

# 4. Queue worker (background jobs)
php artisan queue:work --daemon
```

## 📊 Monitoring

```bash
# Check active subscriptions
php tinker
>>> User::find(1)->pushSubscriptions()->active()->count()

# Check failed deliveries
php artisan queue:failed

# Retry failed
php artisan queue:retry {id}

# Logs
tail -f storage/logs/laravel.log | grep "\[PWA\]\|\[SW\]"
```

## 🎯 Deployment Checklist

- [ ] HTTPS configured
- [ ] Icons generated (192x192, 512x512)
- [ ] `npm run build` executed
- [ ] Migrations run: `php artisan migrate --force`
- [ ] Queue worker running: `php artisan queue:work --daemon`
- [ ] Validation passed: `php artisan pwa:validate`
- [ ] Lighthouse PWA score >= 90
- [ ] Tested on real device (Android/iOS)

---

**Commands Cheat Sheet:**

```bash
# Development
npm run dev                          # Hot reload
npm run build                        # Production build
php artisan serve                    # Start server
php artisan queue:work               # Process jobs

# PWA Specific  
php artisan pwa:validate            # Check config
php artisan notify:test-push        # Send test notification

# Maintenance
php artisan migrate                 # Run migrations
php artisan cache:clear             # Clear caches
php artisan storage:link            # Link storage
```

---

💡 **Tip:** Sempre fazer hard refresh (Cmd+Shift+R / Ctrl+Shift+Del) após mudanças em prod!
