<?php

namespace App\Traits;

trait SuppressesDefaultFilamentNotifications
{
    /**
     * Override Filament's default created notification so it is not sent.
     */
    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return null;
    }
}
