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
        }

        // Redirect to the appropriate panel after password change
        if ($user) {
            $destination = match ($user->role) {
                \App\Enums\UserRole::EMPLOYEE => redirect('/employee'),
                \App\Enums\UserRole::HR => redirect('/hr'),
                \App\Enums\UserRole::ADMIN, \App\Enums\UserRole::ROOT => redirect('/admin'),
                default => redirect('/'),
            };

            return $destination->with('success', 'Password changed successfully!');
        }

        return redirect('/')->with('success', 'Password changed successfully!');
    }
}
