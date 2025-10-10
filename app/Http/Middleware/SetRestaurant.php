<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Restaurant;

class SetRestaurant
{
    public function handle(Request $request, Closure $next): Response
    {
        $restaurantId = null;

        // Opción 1: header
        $header = $request->headers->get('X-Restaurant-ID');
        if ($header) {
            if (ctype_digit((string) $header)) {
                $restaurantId = (int) $header;
            } else {
                $restaurantId = Restaurant::where('slug', $header)->value('id') ?: null;
            }
        }

        // Opción 2: subdominio
        if (!$restaurantId) {
            $host = $request->getHost();                 // p.ej. demo.localhost
            $parts = explode('.', $host);
            $slug = count($parts) > 2 ? $parts[0] : (count($parts) === 2 ? $parts[0] : null);
            if ($slug && !in_array($slug, ['localhost','127','www'])) {
                $restaurantId = Restaurant::where('slug', $slug)->value('id') ?: null;
            }
        }

        // Fallback automático cuando solo existe un restaurante registrado
        if (!$restaurantId) {
            $candidateIds = Restaurant::query()->limit(2)->pluck('id');

            if ($candidateIds->count() === 1) {
                $restaurantId = (int) $candidateIds->first();
            } elseif (app()->environment(['local', 'testing']) && $candidateIds->isNotEmpty()) {
                // En entornos locales o de pruebas usamos el primero disponible
                $restaurantId = (int) $candidateIds->first();
            }
        }

        if ($restaurantId !== null) {
            app()->instance('current_restaurant_id', $restaurantId);
        } else {
            app()->forgetInstance('current_restaurant_id');
        }
        $request->attributes->set('restaurant_id', $restaurantId);

        return $next($request);
    }
}
