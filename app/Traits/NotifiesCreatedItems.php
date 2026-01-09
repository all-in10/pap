<?php

namespace App\Traits;

use Filament\Notifications\Notification;

trait NotifiesCreatedItems
{
    /**
     * Send a separate success notification for each created item.
     *
     * @param array $items Array of strings describing created items
     */
    protected function notifyCreatedItems(array $items): void
    {
        foreach ($items as $item) {
            Notification::make()
                ->title('Criado')
                ->body($item)
                ->success()
                ->send();
        }
    }
}
