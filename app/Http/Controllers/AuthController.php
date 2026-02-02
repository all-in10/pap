<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthController extends Controller
{
    public function attempt(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            // If there was an intended url already (e.g., attempted panel login), override it
            $intended = session()->pull('url.intended', null);
            if ($intended && $user instanceof User) {
                Log::info('Overriding intended panel on login to user panelPath (previous panel login)', [
                    'user_id' => $user->id ?? null,
                    'previous' => $intended,
                    'new_intended' => $user->panelPath(),
                ]);
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
}
