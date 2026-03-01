<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPushSubscription extends Model
{
    use SoftDeletes;

    protected $table = 'user_push_subscriptions';

    protected $fillable = [
        'user_id',
        'endpoint',
        'public_key',
        'auth_secret',
        'user_agent',
        'platform',
    ];

    protected $hidden = [
        'public_key',
        'auth_secret',
    ];

    /**
     * Relationship with User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at')
            ->where('failed_attempts', '<', 5);
    }

    /**
     * Detect platform from user agent
     */
    public static function detectPlatform(string $userAgent): string
    {
        if (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) {
            return 'ios';
        }

        if (str_contains($userAgent, 'Android')) {
            return 'android';
        }

        return 'web';
    }

    /**
     * Mark subscription as failed
     */
    public function markFailed(): void
    {
        $this->increment('failed_attempts');

        // Mark as deleted if too many failures
        if ($this->failed_attempts >= 5) {
            $this->forceDelete();
        }
    }

    /**
     * Mark subscription as valid
     */
    public function markSuccessful(): void
    {
        $this->update([
            'failed_attempts' => 0,
            'last_checked_at' => now(),
        ]);
    }
}
