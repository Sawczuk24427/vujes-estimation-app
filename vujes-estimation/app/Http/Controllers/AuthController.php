<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $user = User::find($request->id);

        if ($user) {
            Auth::login($user);

            return response()->json(['message' => 'Zalogowano pomyślnie!']);

        }

        return response()->json(['error' => 'Nie znaleziono użytkownika'], 404);
    }

    public function logout(Request $request)
    {

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Wylogowano pomyślnie!']);
    }
}
