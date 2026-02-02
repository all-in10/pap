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

        return redirect()->route('filament.admin.pages.employee-dashboard')
            ->with('success', 'Password changed successfully!');
    }
}
