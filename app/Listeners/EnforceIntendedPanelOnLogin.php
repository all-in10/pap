<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\User; // for Intelephense type hints

class EnforceIntendedPanelOnLogin
{
    public function handle(Login $event): void
    {
        /** @var User|null $user */
        $user = $event->user;

        // Ensure we have a session and an intended URL
        $intended = Session::get('url.intended');
        if (empty($intended) || empty($user)) {
            return;
        }

        // If the intended is a panel path and doesn't match the user's panel, overwrite it
        $panels = ['/admin', '/hr', '/employee', '/filament', '/app'];
        foreach ($panels as $panel) {
            if (str_starts_with($intended, $panel) && !str_starts_with($intended, $user->panelPath())) {
                Session::put('url.intended', $user->panelPath());
                Log::info('Overriding intended panel on login to user panelPath', ['user_id' => $user->id, 'new_intended' => $user->panelPath()]);
                return;
            }
        }
    }
}
