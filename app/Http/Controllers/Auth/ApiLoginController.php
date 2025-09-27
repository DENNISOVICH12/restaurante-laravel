<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiLoginController extends Controller
{
    public function login(Request $request)
    {
        $cred = $request->validate([
            'usuario'  => ['required','string'],
            'password' => ['required','string'],
        ]);

        // Importante: Intentamos con el campo 'usuario' (no email).
        if (!Auth::attempt(['usuario' => $cred['usuario'], 'password' => $cred['password']])) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        /** @var \App\Models\Usuario $user */
        $user = $request->user(); // queda autenticado con guard web, pero creamos token de API
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'   => $user->id,
                'nombre' => $user->nombre,
                'usuario' => $user->usuario,
                'rol'  => $user->rol,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['ok' => true]);
    }
    public function authenticated(Request $request, $user)
{
    // Redirigir según el rol del usuario
    if ($user->rol === 'admin') {
        return redirect('/admin/dashboard');
    }

    if ($user->rol === 'empleado') {
        return redirect('/empleado/panel');
    }

    if ($user->rol === 'cliente') {
        return redirect('/cliente/inicio');
    }

    // Si no tiene rol válido, cerrar sesión por seguridad
    Auth::logout();
    return redirect('/login')->withErrors(['rol' => 'Rol no autorizado.']);
}
}
