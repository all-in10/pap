<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
/** @phpstan-ignore-next-line */

class PasswordChangeController extends Controller
{
    /**
     * Show the password change form.
     */
    public function show()
    {
        return view('auth.change-password');
    }

    /**
     * Update the user's password.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
                function ($attribute, $value, $fail) {
                    if (stripos($value, 'password') !== false) {
                        $fail('A palavra-passe não pode conter "password".');
                    }
                },
            ],
        ], [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser uma cadeia de caracteres.',
            'min' => 'A palavra-passe deve ter pelo menos :min caracteres.',
            'confirmed' => 'A confirmação da palavra-passe não coincide.',
            'regex' => 'A palavra-passe deve conter letras maiúsculas, minúsculas, números e símbolos.',
        ]);

        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        // Impede que a nova palavra-passe seja igual à atual
        if ($user && Hash::check($validated['password'], $user->password)) {
            return back()->withErrors(['password' => 'A nova palavra-passe não pode ser igual à atual.'])->withInput();
        }

        if ($user) {
            $user->update([
                'password' => Hash::make($validated['password']),
                'must_change_password' => false,
            ]);
        }

        return redirect('/app')
            ->with('success', 'Palavra-passe alterada com sucesso!');
    }
}
