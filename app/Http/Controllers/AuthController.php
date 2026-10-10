<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required|string',
            'password' => 'required|string|min:4',
        ]);

        if (! Auth::attempt($credentials)) {
            return response()->json(['message' => 'Identifiants incorrects.'], 401);
        }

        $request->session()->regenerate();

        return response()->json(['user' => $request->user()]);
    }

    public function show(Request $request)
    {
        return response()->json(['user' => $request->user()]);
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|string|min:4',
            'password' => 'required|string|min:4',
        ]);

        if (! Hash::check($data['current_password'], $request->user()->password)) {
            return response()->json(['message' => 'Le mot de passe actuel est incorrect.'], 422);
        }

        $request->user()->update(['password' => $data['password']]);

        return response()->json(['message' => 'Mot de passe modifié avec succès.']);
    }
}
