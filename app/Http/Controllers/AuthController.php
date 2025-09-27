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

    $ok = Auth::attempt(
        ['usuario' => $cred['usuario'], 'password' => $cred['password']],
        $request->boolean('remember')
    );

    if (!$ok) {
        Log::warning('LOGIN FAIL', ['u' => $cred['usuario']]);
        return back()->withErrors(['usuario' => 'Credenciales inválidas'])->withInput();
    }

    // MUY importante
    $request->session()->regenerate();

    $user = Auth::user();
    $rol  = strtolower((string)$user->rol);
    Log::info('LOGIN OK', ['id' => $user->id, 'rol' => $rol]);

    $route = match ($rol) {
        'admin'    => 'admin.panel',
        'cocinero' => 'cocina.panel',
        'mesero'   => 'meseros.panel',
        'cliente'  => 'cliente.panel',
        default    => 'cliente.panel',
    };

    // Si llegó aquí porque intentó abrir /administracion sin login,
    // lo devolvemos donde quería entrar; si no, al panel por rol.
    return redirect()->intended(route($route));
}


    public function logout($request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
