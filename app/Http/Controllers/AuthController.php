<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Vista simple de login (ver más abajo)
        return view('auth.login');
    }

    public function doLogin(Request $request)
{
    $cred = $request->validate([
        'usuario'  => ['required','string'],
        'password' => ['required','string'],
    ]);

    // intenta con el campo 'usuario'
    $ok = Auth::attempt(
        ['usuario' => $cred['usuario'], 'password' => $cred['password']],
        $request->boolean('remember')
    );

    if (!$ok) {
        return back()->withErrors(['usuario' => 'Credenciales inválidas'])->withInput();
    }

    // muy importante para fijar la sesión
    $request->session()->regenerate();

    $user = Auth::user();

    return match ($user->rol) {
        'admin'    => redirect()->route('admin.panel'),
        'cocinero' => redirect()->route('cocina.panel'),
        'mesero'   => redirect()->route('meseros.panel'),
        'cliente'  => redirect()->route('cliente.panel'),
        default    => redirect()->route('cliente.panel'),
    };
}


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
