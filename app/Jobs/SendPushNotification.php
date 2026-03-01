<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserPushSubscription;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use WebPush\WebPush;

class SendPushNotification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $title;
    protected $body;
    protected $icon;
    protected $badge;
    protected $data;
    protected $url;

    /**
     * Create a new job instance.
     */
    public function __construct(
        User $user,
        string $title,
        string $body,
        ?string $icon = null,
        ?string $badge = null,
        ?array $data = null,
        ?string $url = null
    ) {
        $this->user = $user;
        $this->title = $title;
        $this->body = $body;
        $this->icon = $icon ?? '/pwa-icons/icon-192x192.png';
        $this->badge = $badge ?? '/pwa-icons/icon-192x192.png';
        $this->data = $data ?? [];
        $this->url = $url ?? config('app.url');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $subscriptions = $this->user->pushSubscriptions()->active()->get();

        if ($subscriptions->isEmpty()) {
            Log::info('[PWA] No active subscriptions for user', ['user_id' => $this->user->id]);
            return;
        }

        $payload = $this->buildPayload();

        foreach ($subscriptions as $subscription) {
            try {
                $this->sendNotification($subscription, $payload);
                $subscription->markSuccessful();
            } catch (\Exception $e) {
                Log::warning('[PWA] Failed to send notification', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
                $subscription->markFailed();
            }
        }
    }

    /**
     * Build notification payload
     */
    protected function buildPayload(): string
    {
        return json_encode([
            'title' => $this->title,
            'body' => $this->body,
            'icon' => $this->icon,
            'badge' => $this->badge,
            'data' => array_merge($this->data, [
                'url' => $this->url,
                'timestamp' => now()->toIso8601String(),
            ]),
        ]);
    }

    /**
     * Send notification to subscription
     * 
     * Uses Web Push Protocol (RFC 8030)
     */
    protected function sendNotification(UserPushSubscription $subscription, string $payload): void
    {
        // If web-push library is installed, use it
        if (class_exists('WebPush\WebPush')) {
            $this->sendWithWebPush($subscription, $payload);
            return;
        }

        // Fallback: Manual HTTP POST to subscription endpoint
        $this->sendManual($subscription, $payload);
    }

    /**
     * Send using web-push-php library (install with: composer require web-push-php/web-push)
     * @psalm-suppress UndefinedClass WebPush library is optional
     * @phpstan-ignore-next-line WebPush library is optional
     */
    protected function sendWithWebPush(UserPushSubscription $subscription, string $payload): void
    {
        try {
            /** @var WebPush $webPush */
            $webPush = new WebPush([
                'VAPID' => [
                    'subject' => 'mailto:'.config('mail.from.address'),
                    'publicKey' => env('VAPID_PUBLIC_KEY'),
                    'privateKey' => env('VAPID_PRIVATE_KEY'),
                ],
            ]);

            $subscriptionObject = [
                'endpoint' => $subscription->endpoint,
                'keys' => [
                    'auth' => $subscription->auth_secret,
                    'p256dh' => $subscription->public_key,
                ],
            ];

            $report = $webPush->sendOneNotification($subscriptionObject, $payload);

            if ($report->isSuccess()) {
                Log::info('[PWA] Notification sent successfully', [
                    'subscription_id' => $subscription->id,
                ]);
            } else {
                throw new \Exception('Web Push report failed: ' . $report->getReason());
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Send manually without web-push library
     * Note: This is a simplified version. Production should use web-push library
     */
    protected function sendManual(UserPushSubscription $subscription, string $payload): void
    {
        // This would require implementing Web Push Protocol manually
        // For now, we'll use curl to simulate
        $this->sendViaSimple($subscription, $payload);
    }

    /**
     * Simple HTTP POST to endpoint (not fully compliant with Web Push RFC, but works for basic testing)
     */
    protected function sendViaSimple(UserPushSubscription $subscription, string $payload): void
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $subscription->endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload),
                'TTL: 24',
            ],
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        /** @phpstan-ignore-next-line curl_close is safe to use */
        curl_close($ch);

        if ($error) {
            throw new \Exception('cURL error: ' . $error);
        }

        if ($httpCode >= 400) {
            throw new \Exception('HTTP ' . $httpCode . ': ' . $response);
        }

        Log::info('[PWA] Simple notification sent', [
            'subscription_id' => $subscription->id,
            'http_code' => $httpCode,
        ]);
    }
}
