<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // make Intelephense understand the user type


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($data, $request->boolean('remember'))) {
            $request->session()->regenerate();
            /** @var User|null $user */
            $user = Auth::user();

            // If there is an intended URL, and it's a panel path (e.g. /admin, /hr, /employee, /filament),
            // avoid redirecting the user to a panel that doesn't match their role. In that case,
            // prefer the user's own panel as a safe fallback.
            $intended = $request->session()->get('url.intended');
            if ($intended && $this->isPanelPath($intended) && !str_starts_with($intended, $user->panelPath())) {
                return redirect()->to($user->panelPath());
            }

            return redirect()->intended($user?->panelPath() ?? '/');
        }

        return back()->withErrors(['email' => 'Credenciais inválidas'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Detect if a given path looks like a panel path we should not honor as intended
     * when it belongs to a different role.
     */
    private function isPanelPath(string $path): bool
    {
        $panels = ['/admin', '/hr', '/employee', '/filament'];
        foreach ($panels as $panel) {
            if (str_starts_with($path, $panel)) {
                return true;
            }
        }
        return false;
    }
}
