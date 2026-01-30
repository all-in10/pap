<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
/** @phpstan-ignore-next-line */

class PasswordChangeController extends Controller
{
    /**
     * Exibe o formulário de alteração de senha
     * Retorna a view para mudança de senha obrigatória
     */
    public function show()
    {
        return view('auth.change-password');
    }

    /**
     * Atualiza a senha do usuário
     * Valida a nova senha, atualiza no banco e redireciona
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        
        if ($user) {
            $user->update([
                'password' => Hash::make($validated['password']),
                'must_change_password' => false,
            ]);

            // Persist a lightweight marker in session with the password hash so
            // concurrent requests don't overwrite the authenticated session and
            // invalidate the login unexpectedly. Then force a session save to
            // ensure the payload is written before we redirect (reduces race
            // conditions with Livewire background requests).
            session()->put('password_hash_web', $user->password);
            session()->save();
        }

        // Redirect the user directly to their panel based on role to avoid extra
        // round-trips through /app which can create redirect loops.
        $target = '/app';
        if ($user) {
            $roleValue = $user->role instanceof \App\Enums\UserRole ? $user->role->value : $user->role;
            if ($roleValue === \App\Enums\UserRole::ROOT->value || $roleValue === \App\Enums\UserRole::ADMIN->value) {
                $target = '/admin';
            } elseif ($roleValue === \App\Enums\UserRole::HR->value) {
                $target = '/hr';
            } elseif ($roleValue === \App\Enums\UserRole::EMPLOYEE->value) {
                $target = '/employee';
            }
        }

        return redirect($target)
            ->with('success', 'Password changed successfully!');
    }
}
