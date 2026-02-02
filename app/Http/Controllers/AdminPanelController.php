<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPanelController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        return view('panels.admin', ['user' => $user]);
    }
}
