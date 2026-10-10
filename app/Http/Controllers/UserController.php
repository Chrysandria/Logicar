<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::query()->orderBy('id')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:users,name',
            'service_sigle' => 'required|string|exists:employes,service_sigle',
        ]);

        $user = User::create([
            ...$data,
            'password' => '0000',
        ]);

        return response()->json([
            'message' => 'Nouvel utilisateur créé avec le mot de passe par défaut "0000".',
            'user' => $user,
        ], 201);
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return response()->json(['message' => 'Vous ne pouvez pas supprimer votre propre compte.'], 409);
        }

        $user->delete();

        return response()->noContent();
    }
}
