<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Procesar el inicio de sesión
     */
    public function doLogin(Request $request)
    {
        // Validar datos enviados desde el formulario
        $credentials = $request->validate([
            'usuario'  => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Intentar autenticación con el campo "usuario"
        if (Auth::attempt([
            'usuario' => $credentials['usuario'],
            'password' => $credentials['password']
        ])) {
            $request->session()->regenerate();

            // Obtener el rol del usuario autenticado
            $user = Auth::user();
            $rol = strtolower($user->rol ?? '');

            // Redirigir según el rol
            return match ($rol) {
                'admin'    => redirect()->route('admin.panel'),
                'cocinero' => redirect()->route('cocina.panel'),
                'mesero'   => redirect()->route('meseros.panel'),
                'cliente'  => redirect()->route('cliente.panel'),
                default    => redirect()->route('cliente.panel'),
            };
        }

        // Si las credenciales fallan, volver con error
        return back()->withErrors([
            'usuario' => 'Credenciales incorrectas o usuario inactivo.'
        ])->onlyInput('usuario');
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidar sesión y token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirigir al login
        return redirect()->route('login');
    }
}
