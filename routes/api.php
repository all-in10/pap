<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PushSubscriptionController;

Route::middleware('auth:sanctum')->group(function () {
    // Push notification subscriptions
    Route::post('/push-subscribe', [PushSubscriptionController::class, 'subscribe']);
    Route::post('/push-unsubscribe', [PushSubscriptionController::class, 'unsubscribe']);
    Route::get('/push-subscription-count', [PushSubscriptionController::class, 'getSubscriptionCount']);
});
