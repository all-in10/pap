<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function me(): JsonResponse
    {
        $user = Auth::user();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->value,
        ]);
    }

    public function logout(): JsonResponse
    {
        Auth::logout();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
