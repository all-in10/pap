<?php

namespace App\Traits;

use Filament\Notifications\Notification;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Auth;

trait NotifiesUpdatedItems
{
    /**
     * Send a separate success notification for each updated item and persist it in history.
     *
     * @param array $items Array of strings describing updated items
     */
    /**
     * @param array $items
     * @param int|null $targetUserId Optional user id to relate the notification to
     */
    protected function notifyUpdatedItems(array $items, ?int $targetUserId = null): void
    {
        foreach ($items as $item) {
            // Persist to notification history
            try {
                NotificationLog::create([
                    'title' => 'Atualizado',
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
                ->title('Atualizado')
                ->body($item)
                ->success()
                ->send();
        }
    }
}