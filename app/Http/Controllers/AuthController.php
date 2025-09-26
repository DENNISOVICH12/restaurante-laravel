<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /** Mostrar login */
    public function showLogin()
    {
        return view('login');
    }

    /** Mostrar registro */
    public function showRegister()
    {
    $isAdmin = strtolower(\Illuminate\Support\Facades\Session::get('user.rol', '')) === 'admin';
    return view('registro', ['isAdmin' => $isAdmin]);
    }
    /** Registrar usuario */
    public function doRegister(Request $request)
    {
    // Si quien registra NO es admin, siempre forzamos a 'cliente'
        $isAdmin = strtolower(\Session::get('user.rol', '')) === 'admin';

    // Validación
        $request->validate([
            'nombre'   => 'required|string|min:3',
            'correo'   => 'required|email|unique:usuarios,correo',
            'password' => 'required|min:5',
        // el admin SÍ puede elegir rol de la lista (ahora incluye 'cliente')
            'rol'      => $isAdmin ? 'required|in:admin,cocinero,mesero,empleado,cliente' : 'nullable',
        ]);

    // Rol final: si NO es admin => siempre 'cliente'
        $rol = $isAdmin
            ? strtolower($request->input('rol', 'cliente'))
            : 'cliente';

    // Insert (bcrypt)
        DB::table('usuarios')->insert([
            'nombre'   => $request->nombre,
            'correo'   => $request->correo,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'rol'      => $rol,
            'activo'   => 1,
        ]);

        return redirect('/login')->with('success', 'Cuenta creada. Inicia sesión.');
    }


    /** Autenticar usuario */
    public function doLogin(Request $request)
    {
        $request->validate([
            'correo'   => 'required|email',
            'password' => 'required',
        ]);

        $user = DB::table('usuarios')
            ->where('correo', $request->correo)
            ->where('activo', 1)
            ->first();

        if (!$user) {
            return back()->with('error', 'Usuario no encontrado o inactivo')->withInput();
        }

        // Compatibilidad: texto plano / md5 / bcrypt
        $pass = $request->password;
        $ok = false;

        if ($user->password === $pass) {
            $ok = true; // texto plano (legacy)
        } elseif (md5($pass) === $user->password) {
            $ok = true; // md5 (legacy)
        } elseif (Hash::check($pass, $user->password)) {
            $ok = true; // bcrypt (actual)
        }

        if (!$ok) {
            return back()->with('error', 'Contraseña incorrecta')->withInput();
        }

        // Normalizamos rol a minúsculas
        $rol = strtolower($user->rol ?? 'cliente');

        // ⚠️ Si en BD tienes 'meseros' (plural), MIGRA a 'mesero'.
        // Mientras tanto, si quieres tolerar ambos:
        // if ($rol === 'meseros') { $rol = 'mesero'; }

        // Guardar sesión mínima y regenerar
        Session::put('user', [
            'id'     => $user->id,
            'nombre' => $user->nombre ?? '',
            'correo' => $user->correo,
            'rol'    => $rol,
        ]);
        $request->session()->regenerate();

        // URL de destino por rol (unificado a 'mesero')
        $redirect = match ($rol) {
            'admin'    => redirect()->route('admin.panel'),
            'cocinero' => redirect()->route('cocina.panel'),
            'mesero'   => redirect()->route('meseros.panel'),
            'cliente'  => redirect()->route('cliente.panel'),
            default    => redirect()->route('cliente.panel'),
        };

        // Si el login llega vía fetch/AJAX, responde JSON con la URL
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'redirect' => $redirect]);
        }

        // En submit normal, deja que el navegador siga el 302
        return redirect($redirect);

        return match (strtolower($user->rol ?? '')) {
        'admin'    => redirect('/administracion'),
        'cocinero' => redirect('/cocina'),
        'mesero'   => redirect('/meseros'),
        'empleado' => redirect('/dashboard'), // si aún usas 'empleado'
        'cliente'  => redirect('/dashboard'), // nuevo rol
        default    => redirect('/dashboard'),
};

    }

    /** Cerrar sesión */
    public function logout(Request $request)
    {
        Session::flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
  
}
