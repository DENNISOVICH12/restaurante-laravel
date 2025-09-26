<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Vista pública de orden por QR (puede venir mesa en la URL).
     */
    public function orden(Request $request, ?string $mesa = null)
    {
        return view('orden', ['mesa' => $mesa]);
    }

    /**
     * Login simple (vista).
     */
    public function login()
    {
        return view('login');
    }

    /**
     * Panel de meseros (valida sesión y rol; devuelve vista 'meseros').
     * NOTA: ya protegemos con middleware 'role:admin,mesero', pero
     * dejamos una validación mínima por si llegaran aquí sin middleware.
     */
    public function meseros(Request $request)
    {
        $user = session('user');

        if (!$user || !in_array(strtolower($user['rol'] ?? ''), ['mesero','admin'])) {
            return redirect('/login')->with('error', 'Debes iniciar sesión como mesero.');
        }

        // DEVOLVEMOS LA VISTA PLURAL
        return view('meseros');
    }
}
