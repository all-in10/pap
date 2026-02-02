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

        // Ensure we have a session and a user
        $intended = Session::get('url.intended');
        $previous = Session::get('_previous.url') ?? (Session::get('_previous')['url'] ?? null);
        if (empty($user)) {
            return;
        }

        // If there is an intended URL and it points to a panel that doesn't match the user's panel, overwrite it.
        // Additionally, if the login flow originated from a panel login/URL that doesn't match the user's panel,
        // set the intended to the user's canonical panel so they land in the correct place.
        $panels = ['/admin', '/hr', '/employee', '/filament', '/app'];

        // Check existing intended
        if (! empty($intended)) {
            foreach ($panels as $panel) {
                if (str_starts_with($intended, $panel) && ! str_starts_with($intended, $user->panelPath())) {
                    Session::put('url.intended', $user->panelPath());
                    Log::info('Overriding intended panel on login to user panelPath (intended mismatch)', ['user_id' => $user->id, 'new_intended' => $user->panelPath()]);
                    return;
                }
            }
        }

        // If previous URL indicates user submitted a login from a panel that doesn't match their role,
        // force intended to the user's panel.
        if (! empty($previous)) {
            $first = explode('/', trim(parse_url($previous, PHP_URL_PATH), '/'))[0] ?? null;
            if ($first !== null && in_array('/' . $first, $panels, true)) {
                if (! str_starts_with('/' . $first, $user->panelPath())) {
                    Session::put('url.intended', $user->panelPath());
                    Log::info('Overriding intended panel on login to user panelPath (previous panel login)', ['user_id' => $user->id, 'previous' => $previous, 'new_intended' => $user->panelPath()]);
                    return;
                }
            }
        }
    }
}
