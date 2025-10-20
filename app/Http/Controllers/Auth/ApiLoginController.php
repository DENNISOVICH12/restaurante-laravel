<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ApiLoginController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'usuario' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('usuario', $data['usuario'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'error' => [
                    'code' => 401,
                    'message' => 'Credenciales incorrectas'
                ]
            ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'token' => $token,
            'user' => $user
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout OK']);
    }
}
