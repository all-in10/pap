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
    protected function notifyCreatedItems(array $items): void
    {
        foreach ($items as $item) {
            // Persist to notification history
            try {
                NotificationLog::create([
                    'title' => 'Criado',
                    'body' => $item,
                    'data' => null,
                    'created_by' => Auth::id(),
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
