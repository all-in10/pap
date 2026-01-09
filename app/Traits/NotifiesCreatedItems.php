<?php

namespace App\Traits;

use Filament\Notifications\Notification;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Auth;

trait NotifiesCreatedItems
{
    /**
     * Send a separate success notification for each created item and persist it in history.
     *
     * @param array $items Array of strings describing created items
     */
    /**
     * @param array $items
     * @param int|null $targetUserId Optional user id to relate the notification to
     */
    protected function notifyCreatedItems(array $items, ?int $targetUserId = null): void
    {
        foreach ($items as $item) {
            // Persist to notification history
            try {
                NotificationLog::create([
                    'title' => 'Criado',
                    'body' => $item,
                    'data' => null,
                    'created_by' => Auth::id(),
                    'user_id' => $targetUserId,
                ]);
            } catch (\Throwable $e) {
                // If persisting fails, don't block UI notifications
            }

            // Send the Filament notification
            Notification::make()
                ->title('Criado')
                ->body($item)
                ->success()
                ->send();
        }
    }
}
