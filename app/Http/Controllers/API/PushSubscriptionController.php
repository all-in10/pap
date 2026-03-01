<?php

namespace App\Http\Controllers\API;

use App\Models\UserPushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PushSubscriptionController extends Controller
{
    /**
     * Register or update push subscription
     * 
     * POST /api/push-subscribe
     */
    public function subscribe(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        // Validate push subscription
        $validated = $request->validate([
            'endpoint' => 'required|url',
            'public_key' => 'required|string',
            'auth_secret' => 'required|string',
        ]);

        try {
            // Find or create subscription
            $subscription = UserPushSubscription::updateOrCreate(
                ['endpoint' => $validated['endpoint']],
                [
                    'user_id' => $user->id,
                    'public_key' => $validated['public_key'],
                    'auth_secret' => $validated['auth_secret'],
                    'user_agent' => $request->userAgent(),
                    'platform' => UserPushSubscription::detectPlatform($request->userAgent()),
                    'last_checked_at' => now(),
                    'failed_attempts' => 0,
                ]
            );

            // Restore if was soft-deleted
            if ($subscription->trashed()) {
                $subscription->restore();
            }

            return response()->json([
                'success' => true,
                'message' => 'Subscription registered successfully',
                'data' => [
                    'subscription_id' => $subscription->id,
                    'platform' => $subscription->platform,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Push subscription error:', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to register subscription',
            ], 422);
        }
    }

    /**
     * Unsubscribe from push notifications
     * 
     * POST /api/push-unsubscribe
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $validated = $request->validate([
            'endpoint' => 'required|url',
        ]);

        try {
            UserPushSubscription::where('endpoint', $validated['endpoint'])
                ->where('user_id', $user->id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Unsubscribed successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Push unsubscribe error:', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to unsubscribe',
            ], 422);
        }
    }

    /**
     * Get user subscription count
     */
    public function getSubscriptionCount(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        /** @var \App\Models\User $user */
        return response()->json([
            'count' => $user->pushSubscriptions()->active()->count(),
        ]);
    }
}
