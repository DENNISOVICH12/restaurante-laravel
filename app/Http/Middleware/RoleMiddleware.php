<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RoleMiddleware
{
    /**
     * Verifica que el usuario tenga alguno de los roles permitidos.
     * Uso: ->middleware('role:admin,mesero')
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user || !in_array($user->rol, $roles, true)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        return $next($request);
        
        $rol = strtolower($user['rol'] ?? '');
        $roles = array_map('strtolower', $roles);

        if (!in_array($rol, $roles, true)) {
            // Redirección por rol cuando no coincide
            return match ($rol) {
                'admin'    => redirect()->route('admin.panel'),
                'cocinero' => redirect()->route('cocina.panel'),
                'mesero'   => redirect()->route('meseros.panel'),
                default    => redirect()->route('cliente.panel'),
            };
        }

        return $next($request);
    }
}
