<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required|string',
            'password' => 'required|string|min:4',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $request->session()->regenerate();
            return response()->json(['message' => 'Login successful', 'user' => $user], 200);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }

    public function reset(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required|string',
            'password' => 'required|string|min:4',
            "newPassword" => 'required|string|min:4'
        ]);

        if(Auth::attempt(["name" => $credentials["name"], "password" => $credentials["password"]])) {
            User::where("name","=",$request->name,"and")->update([
                "password" => bcrypt($request->newPassword)
            ]);

            return response()->json(["message" => "Modification enregistrée."]);
        }
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Logout successful'], 200);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'service' => 'required|string',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'password' => bcrypt("0000"),
            'service_sigle' => $data['service'],
        ]);

        return response()->json(['message' => 'Nouvel utilisateur créé avec le mot de passe par defaut "0000" ', 'user' => $user], 201);
    }

    public function getUser()
    {
        if (Auth::check()) {
            return response()->json(["user" => Auth::user(), "connected" => true]);
        } else {
            return response()->json(["user" => null, "connected" => false]);
        }
    }

    public function delete(Request $request){
        $validate = $request->validate([
            "id" => "required|integer"
        ]);

        if($validate){
            User::where("id","=",$request->id,"and")->delete();

            return response()->json(["message" => "Cette utilisateur a été supprimé avec succés."]);
        }
    }

    public function AllUsers()
    {
        return response()->json(User::all());
    }
}
