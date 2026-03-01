# PWA Implementation Guide - TeamCore

Complete PWA (Progressive Web App) implementation for TeamCore Laravel project with offline support, push notifications, and installable app features.

## Table of Contents

1. [Quick Start](#quick-start)
2. [Architecture](#architecture)
3. [Features Implemented](#features-implemented)
4. [Testing Guide](#testing-guide)
5. [Production Deployment](#production-deployment)
6. [Troubleshooting](#troubleshooting)

---

## Quick Start

### 1. Install Dependencies

```bash
# No additional dependencies needed for basic PWA
# For Web Push notifications (optional), add:
composer require web-push-php/web-push
```

### 2. Generate Icons

```bash
cd public/pwa-icons
php generate-icons.php
```

This generates:
- `icon-192x192.png` - Home screen icon
- `icon-512x512.png` - Splash screen & store
- `maskable-icon-192x192.png` - Adaptive icon for iOS

### 3. Build and Run

```bash
# Compile Vite (includes Service Worker compilation)
npm run build

# For development with hot-reload
npm run dev

# Run migrations
php artisan migrate

# Validate PWA configuration
php artisan pwa:validate
```

### 4. Start the Application

```bash
# Start development server
php artisan serve

# In another terminal, start queue worker (for push notifications)
php artisan queue:work
```

Visit `http://localhost:8000` and the PWA should be installed.

---

## Architecture

### Service Worker Flow (Network-First Strategy)

```
User Request
    ↓
┌─────────────────────────┐
│ Try Network First       │
│ (Always prefer fresh)   │
└────────────┬────────────┘
             ↓ (Success)
        Return Response  (Cache it)
             ↓ (Failure)
┌─────────────────────────┐
│ Try Cache as Fallback   │
└────────────┬────────────┘
             ↓ (Found)
        Return Cached
             ↓ (Not found)
┌─────────────────────────┐
│ Return Offline Page (/offline)
└─────────────────────────┘
```

### File Structure

```
/
├── public/
│   ├── manifest.json              # PWA metadata
│   ├── css/sw.js                  # Compiled Service Worker
│   └── pwa-icons/
│       ├── icon-192x192.png
│       ├── icon-512x512.png
│       ├── maskable-icon-192x192.png
│       └── generate-icons.php     # Icon generator
│
├── resources/
│   ├── js/
│   │   └── service-worker.js      # Service Worker source
│   └── views/
│       └── offline.blade.php      # Offline fallback page
│
├── app/
│   ├── Http/
│   │   ├── Middleware/
│   │   │   └── PWAHeadersMiddleware.php  # Cache headers
│   │   └── Controllers/
│   │       └── API/
│   │           └── PushSubscriptionController.php
│   ├── Console/
│   │   └── Commands/
│   │       ├── SendTestPushNotification.php
│   │       └── ValidatePWAConfiguration.php
│   ├── Models/
│   │   └── UserPushSubscription.php     # Push subscription model
│   └── Jobs/
│       └── SendPushNotification.php     # Push notification queue job
│
├── database/
│   └── migrations/
│       └── 2024_03_01_000000_create_user_push_subscriptions_table.php
│
├── routes/
│   ├── api.php                   # API endpoints for PWA
│   └── web.php                   # Including /offline route
│
└── vite.config.js               # Updated with Service Worker build
```

---

## Features Implemented

### ✅ Core PWA Features

#### 1. **Offline Support**
- Network-first strategy prioritizes fresh data
- Cache fallback when offline
- Dedicated offline page `/offline`
- Smart cache busting with version management

#### 2. **Installability**
- Web App Manifest (`manifest.json`)
- Apple touch icons for iOS
- App shortcuts (Dashboard, New Employee, Profile)
- Theme colors for browser UI

#### 3. **Service Worker**
- Asset caching on install
- Network request interception
- Background sync capability
- Push notification handling

#### 4. **Push Notifications**
- Web Push API integration
- Backend queue-based delivery
- User subscription management
- Failure handling and retry logic

#### 5. **HTTP Headers**
- Proper cache control per content type
- Service-Worker-Allowed header
- Security headers (X-Content-Type-Options, etc)

### 📱 Supported Features by Platform

#### Android (Chrome, Firefox, Edge)
- ✅ Install as App
- ✅ Offline access
- ✅ Push notifications
- ✅ App shortcuts
- ✅ Fullscreen mode

#### iOS (Safari 16.4+)
- ✅ Install to Home Screen
- ✅ Offline access (limited)
- ✅ Push notifications (with limitations)
- ✅ Status bar styling
- ⚠️ Limited background sync

#### Desktop (Chrome, Edge, Firefox)
- ✅ Install as Window/App
- ✅ Full offline access
- ✅ Push notifications
- ✅ App shortcuts

---

## Testing Guide

### 1. Browser Setup

#### Chrome DevTools

**Location:** Chrome Menu → More tools → Developer tools (F12)

**Tabs to use:**
- **Application tab:**
  - Service Workers: Check registration status
  - Cache Storage: View cached files
  - Manifest: Verify manifest.json
  
- **Network tab:**
  - Check "Offline" checkbox to simulate offline
  - Monitor requests to service-worker.js (should be no-cache)
  - Verify Cache-Control headers

- **Console tab:**
  - Look for "[SW]" logs from Service Worker
  - Check for "[PWA]" logs from registration

### 2. Local Testing (Development)

#### Test 1: Service Worker Registration

```bash
# Open browser console (F12 → Console)
# You should see:
[PWA] Service Worker registered successfully
[SW] Service Worker loaded
```

#### Test 2: Offline Mode

```bash
1. Open Chrome DevTools (F12)
2. Go to Network tab
3. Check "Offline" checkbox
4. Refresh page (Ctrl+R)
5. You should see:
   - Cached assets load
   - API fails gracefully
   - "/offline" page as fallback
```

#### Test 3: Cache Storage

```bash
1. DevTools → Application → Cache Storage
2. Expand "teamcore-v1" cache
3. Should contain:
   - /
   - /offline
   - /js/app.js
   - /css/app.css
   - /manifest.json
   - Other cached assets
```

#### Test 4: Install Prompt (Mobile Chrome)

```bash
1. Open on Android device in Chrome
2. Menu (⋮) → "Install app"
3. Or: Will auto-prompt after 30 seconds on first visit
4. App appears on home screen
```

### 3. API Testing

#### Register for Push Notifications

```bash
# Get CSRF token from cookies or html
curl -X POST http://localhost:8000/api/push-subscribe \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TOKEN" \
  -d '{
    "endpoint": "https://fcm.googleapis.com/...",
    "public_key": "base64-encoded-key",
    "auth_secret": "base64-encoded-secret"
  }'
```

#### Send Test Push Notification

```bash
# Using Artisan command
php artisan notify:test-push --user=1

# With custom message
php artisan notify:test-push --user=1 --title="Test" --body="Message"

# Make sure queue worker is running
php artisan queue:work
```

### 4. Chrome DevTools Network Inspection

#### Check Service Worker Headers

```bash
F12 → Network → Filter: "sw.js"
Look for headers:
  Cache-Control: public, no-cache, no-store, must-revalidate
  Service-Worker-Allowed: /
  Content-Type: application/javascript
```

#### Check Manifest Headers

```bash
F12 → Network → Filter: "manifest.json"
Look for headers:
  Cache-Control: public, max-age=86400
  Content-Type: application/manifest+json
```

### 5. Lighthouse Audit

```bash
1. Chrome DevTools (F12)
2. Lighthouse tab
3. Select "PWA"
4. Audit
5. Should pass:
   - Service Worker
   - HTTPS redirect
   - Web App Manifest
   - Icon size requirements
```

---

## Validation

### Check PWA Configuration

```bash
php artisan pwa:validate

# Output example:
# ═══════════════════════════════════════
# PWA Configuration Validation
# ═══════════════════════════════════════
# ✓ Service Worker:      Service Worker configured at ./public/js/sw.js
# ✓ Manifest:            Manifest valid with 3 icons
# ✓ Icons:               All PWA icons present
# ✓ Middleware:          PWA Middleware configured
# ✓ API Routes:          API routes configured
# ✓ Environment:         Environment configuration OK
# 
# Results: 6 passed, 0 failed
```

---

## Production Deployment

### Requirements

1. **HTTPS (mandatory)**
   - Service Workers only work on HTTPS
   - Set `APP_URL=https://yourdomain.com` in `.env`

2. **Optimal Settings**

```env
# .env
APP_URL=https://yourdomain.com
QUEUE_CONNECTION=database    # or redis (not sync)
CACHE_DRIVER=redis           # for performance
SESSION_DRIVER=cookie
```

3. **Nginx Configuration**

```nginx
# Add to server block
add_header Service-Worker-Allowed "/";
add_header X-Content-Type-Options "nosniff";

# Service Worker cache policy
location ~ /js/sw.js$ {
    add_header Cache-Control "public, no-cache, no-store, must-revalidate";
    add_header Pragma "no-cache";
}

# Manifest cache policy
location ~ /manifest.json$ {
    add_header Cache-Control "public, max-age=86400";
    add_header Content-Type "application/manifest+json; charset=utf-8";
}
```

4. **Build for Production**

```bash
# Compile all assets including Service Worker
npm run build

# Migrate database
php artisan migrate --force

# Clear caches
php artisan cache:clear
php artisan route:cache

# Start queue workers
php artisan queue:work --daemon

# Validate PWA
php artisan pwa:validate
```

5. **Web Push Notifications Setup (Optional)**

```bash
# Install web-push library for proper encryption
composer require web-push-php/web-push

# Generate VAPID keys
npx web-push generate-vapid-keys

# Add to .env
VAPID_PUBLIC_KEY=your_public_key
VAPID_PRIVATE_KEY=your_private_key
```

---

## Troubleshooting

### Issue: Service Worker Not Registering

**Symptoms:**
- No "[PWA]" logs in console
- DevTools → Application → Service Workers shows empty

**Solutions:**
```bash
# 1. Check if Service Worker is compiled
ls public/js/sw.js

# 2. If missing, rebuild
npm run build

# 3. Check browser console for errors (F12 → Console)

# 4. Ensure HTTPS in production

# 5. Check Network tab for sw.js HTTP 200 status
```

### Issue: Service Worker Not Caching

**Symptoms:**
- DevTools → Application → Cache Storage is empty
- Offline doesn't work

**Solutions:**
```bash
# 1. Check if Service Worker is active
DevTools → Application → Service Workers
Look for "activated and running" status

# 2. Unregister old Service Worker
DevTools → Application → Service Workers → Unregister

# 3. Do hard refresh (Cmd+Shift+R or Ctrl+Shift+Del)

# 4. Check service-worker.js for JavaScript errors
# Look for console logs: [SW] ...
```

### Issue: App Won't Install

**Symptoms:**
- No install prompt on Chrome
- Menu option missing

**Solutions:**
```bash
# 1. Use Chrome Devtools simulator:
DevTools → Console → Paste:
window.PWA.installPrompt?.prompt()

# 2. Wait 30-60 seconds on first visit
# (Chrome requires time before showing prompt)

# 3. Check manifest.json validity
DevTools → Application → Manifest

# 4. Verify HTTPS (required for real devices)

# 5. Check icon sizes
# Should be exactly 192x192 and 512x512 or larger
```

### Issue: Push Notifications Not Arriving

**Symptoms:**
- Test command runs but no notification appears

**Solutions:**
```bash
# 1. Check queue worker is running
ps aux | grep "queue:work"

# 2. Start queue if not running
php artisan queue:work

# 3. Check database queue
php artisan queue:failed

# 4. Verify user has active subscriptions
php tinker
>>> User::find(1)->pushSubscriptions()->active()->count()

# 5. Check logs for errors
tail -f storage/logs/laravel.log

# 6. Check Service Worker console
DevTools → Application → Service Workers → (click SW) → Console
```

### Issue: Offline Page Shows HTTP Error

**Symptoms:**
- "/offline" route returns 404 or blank page

**Solutions:**
```bash
# 1. Check route exists
php artisan route:list | grep offline

# 2. Verify blade file exists
ls resources/views/offline.blade.php

# 3. Test directly
curl http://localhost:8000/offline

# 4. Check Service Worker is catching errors
DevTools → F12 → Console → [SW] logs
```

### Issue: Cache Headers Not Applying

**Symptoms:**
- Everything caches too long
- Updates not appearing

**Solutions:**
```bash
# 1. Check middleware is registered
grep "PWAHeadersMiddleware" bootstrap/app.php

# 2. Verify middleware is loaded
php artisan route:list
Look for "Middleware" column → PWAHeadersMiddleware

# 3. Check response headers
curl -i http://localhost:8000/js/app.js | grep Cache-Control

# 4. Clear application caches
php artisan cache:clear
php artisan route:cache:clear
```

---

## API Reference

### Push Subscription Endpoints

#### Register Subscription

```http
POST /api/push-subscribe
Authorization: Bearer {token}
Content-Type: application/json

{
  "endpoint": "https://fcm.googleapis.com/...",
  "public_key": "base64-key",
  "auth_secret": "base64-secret"
}

Response:
{
  "success": true,
  "message": "Subscription registered successfully",
  "data": {
    "subscription_id": 1,
    "platform": "android"
  }
}
```

#### Unsubscribe

```http
POST /api/push-unsubscribe
Authorization: Bearer {token}
Content-Type: application/json

{
  "endpoint": "https://fcm.googleapis.com/..."
}

Response:
{
  "success": true,
  "message": "Unsubscribed successfully"
}
```

#### Get Subscription Count

```http
GET /api/push-subscription-count
Authorization: Bearer {token}

Response:
{
  "count": 2
}
```

---

## Frontend Integration

### Register for Push Notifications (JavaScript)

```javascript
// resources/js/push-manager.js

class PushNotificationManager {
  async subscribe() {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
      console.error('Push notifications not supported');
      return;
    }

    try {
      const registration = await navigator.serviceWorker.ready;
      
      const subscription = await registration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: this.urlBase64ToUint8Array(
          document.querySelector('meta[name="vapid-key"]')?.content
        ),
      });

      // Send to backend
      await fetch('/api/push-subscribe', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
        },
        body: JSON.stringify({
          endpoint: subscription.endpoint,
          public_key: this.arrayBufferToBase64(subscription.getKey('p256dh')),
          auth_secret: this.arrayBufferToBase64(subscription.getKey('auth')),
        }),
      });

      console.log('✓ Subscribed to push notifications');
    } catch (error) {
      console.error('Push subscription failed:', error);
    }
  }

  urlBase64ToUint8Array(base64String) {
    // Implementation...
  }

  arrayBufferToBase64(buffer) {
    // Implementation...
  }
}

// Usage
const pushManager = new PushNotificationManager();
pushManager.subscribe();
```

---

## Performance Metrics

### Expectations

| Metric | Before PWA | With PWA |
|--------|-----------|---------|
| First Load | ~2.5s | ~2.5s |
| Repeat Load | ~1.8s | ~0.3s (cached) |
| Offline | ❌ Broken | ✅ Works |
| Time to Interactive (TTI) | ~3.2s | ~1.2s |
| Install Size | N/A | ~50MB (browser) |

### Lighthouse Scores

Target: PWA >= 90

```
✓ Performance: 90+
✓ Accessibility: 95+
✓ Best Practices: 95+
✓ SEO: 95+
✓ PWA Compliance: 100
```

---

## Support & Documentation

- **Service Workers:** https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API
- **Web App Manifest:** https://www.w3.org/TR/appmanifest/
- **Web Push Protocol:** https://datatracker.ietf.org/doc/html/rfc8030
- **Laravel Queue:** https://laravel.com/docs/queue

---

**Last Updated:** March 1, 2026
**Version:** 1.0.0 PWA Complete
