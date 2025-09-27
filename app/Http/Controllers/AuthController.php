<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectForRole(Auth::user());
        }

        return view('auth.login');
    }

    public function doLogin(Request $request)
    {
        $cred = $request->validate([
            'usuario'  => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login = trim($cred['usuario']);
        $remember = $request->boolean('remember');

        $attempted = $this->attemptLogin(['usuario' => $login, 'password' => $cred['password']], $remember);

        if (!$attempted && filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $attempted = $this->attemptLogin(['correo' => $login, 'password' => $cred['password']], $remember);
        }

        if (!$attempted) {
            return back()->withErrors([
                'usuario' => 'Las credenciales proporcionadas no son válidas.',
            ])->withInput();
        }

        $request->session()->regenerate();

        return $this->redirectForRole(Auth::user());
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    protected function attemptLogin(array $credentials, bool $remember = false): bool
    {
        $credentials['activo'] = true;

        return Auth::attempt($credentials, $remember);
    }

    protected function redirectForRole(Usuario $user)
    {
        return match (strtolower($user->rol)) {
            'admin'    => redirect()->route('admin.panel'),
            'cocinero' => redirect()->route('cocina.panel'),
            'mesero'   => redirect()->route('meseros.panel'),
            'cliente'  => redirect()->route('cliente.panel'),
            default    => redirect()->route('cliente.panel'),
        };
    }
}