<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'usuario' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = Usuario::where('usuario', $credentials['usuario'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['error' => 'Credenciales incorrectas'], 401);
        }

        $token = $user->createToken('api_token', [$user->rol])->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'token' => $token,
            'rol' => $user->rol,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }
}
