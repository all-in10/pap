<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserPasswordController extends Controller
{
    public function showChangeForm()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();
        $user->password = Hash::make($request->input('password'));
        $user->must_change_password = false;
        $user->password_changed_at = now();
        $user->save();

        return redirect()->intended('/')->with('status', 'Senha atualizada com sucesso.');
    }
}
