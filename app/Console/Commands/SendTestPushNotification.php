<?php

namespace App\Console\Commands;

use App\Jobs\SendPushNotification;
use App\Models\User;
use Illuminate\Console\Command;

class SendTestPushNotification extends Command
{
    protected $signature = 'notify:test-push {--user=1 : User ID} {--title="Test Notification" : Notification title} {--body="This is a test push notification from TeamCore" : Notification body}';

    protected $description = 'Send a test push notification to a user';

    public function handle()
    {
        $userId = $this->option('user');
        $title = $this->option('title');
        $body = $this->option('body');

        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found");
            return 1;
        }

        $subscriptionCount = $user->pushSubscriptions()->active()->count();

        if ($subscriptionCount === 0) {
            $this->warn("No active subscriptions for user {$user->name}");
            $this->info('Make sure the PWA is properly registered and user has accepted notifications');
            return 1;
        }

        $this->info("Sending test notification to {$user->name}...");
        $this->info("Title: {$title}");
        $this->info("Body: {$body}");
        $this->info("Subscriptions: {$subscriptionCount}");

        SendPushNotification::dispatch(
            $user,
            $title,
            $body,
            '/pwa-icons/icon-192x192.png',
            '/pwa-icons/icon-192x192.png',
            [
                'type' => 'test',
                'timestamp' => now()->timestamp,
            ],
            route('dashboard')
        );

        $this->info('✓ Test notification queued successfully!');
        $this->info('Make sure your queue worker is running: php artisan queue:work');

        return 0;
    }
}
