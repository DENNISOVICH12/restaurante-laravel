<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();         // con el guard 'web' ya debe existir
    if (!$user || !in_array($user->rol, $roles, true)) {
        return redirect()->route('login');
    }

        // compara por ejemplo: admin, cocinero, mesero, cliente
        if (!in_array($user->rol, $roles, true)) {
            // sin permiso
            return abort(403, 'No autorizado.');
        }

        return $next($request);
    }
}