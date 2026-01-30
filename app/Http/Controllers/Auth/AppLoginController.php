<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Cache\RateLimiter;

class AppLoginController extends Controller
{
    protected RateLimiter $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    // Exibe o formulário de login unificado
    public function show()
    {
        return view('auth.app-login');
    }

    // Processa tentativa de login (aceita email ou username)
    public function login(Request $request)
    {
        $validated = $request->validate([
            'identity' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        $identity = (string) $validated['identity'];
        $password = (string) $validated['password'];
        $remember = (bool) ($validated['remember'] ?? false);

        $key = Str::lower($identity) . '|' . $request->ip();
        $maxAttempts = 5;
        $decaySeconds = 60; // throttle window

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            $seconds = $this->limiter->availableIn($key);
            Log::warning('Login locked out', ['identity' => $identity, 'ip' => $request->ip(), 'available_in' => $seconds]);

            throw ValidationException::withMessages([
                'identity' => ["Too many login attempts. Try again in {$seconds} seconds."],
            ]);
        }

        // Decide whether identity is email or username
        $credentials = [];
        if (filter_var($identity, FILTER_VALIDATE_EMAIL)) {
            $credentials = ['email' => $identity, 'password' => $password];
        } else {
            $credentials = ['username' => $identity, 'password' => $password];
        }

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Clear attempts on success
            $this->limiter->clear($key);

            // If the user must change password, route them there first
            if ($user->must_change_password) {
                return redirect()->route('password.change');
            }

            return match ($user->role) {
                \App\Enums\UserRole::EMPLOYEE => redirect('/employee'),
                \App\Enums\UserRole::HR => redirect('/hr'),
                \App\Enums\UserRole::ADMIN, \App\Enums\UserRole::ROOT => redirect('/admin'),
                default => redirect('/'),
            };
        }

        // Failed attempt: increment throttle and log
        $this->limiter->hit($key, $decaySeconds);
        Log::warning('Failed login attempt', [
            'identity' => $identity,
            'ip' => $request->ip(),
            'ua' => $request->userAgent(),
        ]);

        throw ValidationException::withMessages([
            'identity' => [trans('auth.failed')],
        ]);
    }

    // Logout helper (optional)
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logged out');
    }
}
