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

        // Fallback en local: primer restaurante
        if (!$restaurantId && app()->environment('local')) {
            $restaurantId = Restaurant::value('id');
        }

        app()->instance('current_restaurant_id', $restaurantId);
        $request->attributes->set('restaurant_id', $restaurantId);

        return $next($request);
    }
}
