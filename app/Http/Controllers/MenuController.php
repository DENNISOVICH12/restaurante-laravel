<?php

namespace App\Http\Controllers;

use App\Models\Menu;  
use App\Models\MenuItem;   // usa tu modelo real; si usas Platos/Bebidas, cambia abajo
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/menu",
     *   tags={"Menú"},
     *   summary="Obtener menú armado",
     *   description="Devuelve el menú agrupado por categoría (plato, bebida, postre) solo ítems disponibles.",
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(type="object",
     *       @OA\Property(property="platos", type="array", @OA\Items(type="object")),
     *       @OA\Property(property="bebidas", type="array", @OA\Items(type="object")),
     *       @OA\Property(property="postres", type="array", @OA\Items(type="object"))
     *     )
     *   )
     * )
     */
    public function index()
    {
        // Si tu menú se arma desde menu_items:
        $items = MenuItem::where('disponible', true)->get()->groupBy('categoria');

        return response()->json([
            'platos'  => array_values(($items['plato']  ?? collect())->toArray()),
            'bebidas' => array_values(($items['bebida'] ?? collect())->toArray()),
            'postres' => array_values(($items['postre'] ?? collect())->toArray()),
        ]);
    }
    public function showToday()
{
    $now = now();

    $menu = Menu::with('items.category')
        ->where('activo', true)
        ->where(function($q) use ($now) {
            $q->whereNull('vigencia_desde')->orWhere('vigencia_desde', '<=', $now);
        })
        ->where(function($q) use ($now) {
            $q->whereNull('vigencia_hasta')->orWhere('vigencia_hasta', '>=', $now);
        })
        ->latest()
        ->first();

    $items = $menu ? $menu->items : MenuItem::where('activo', true)->get();

    $items = $items->map(function($item) {
        return [
            'id' => $item->id,
            'nombre' => $item->nombre,
            'categoria' => $item->category?->nombre,
            'precio' => $item->pivot->precio_override ?? $item->precio_base,
        ];
    })->groupBy('categoria');

    return response()->json([
        'menu' => $menu?->only(['id','nombre','vigencia_desde','vigencia_hasta']),
        'secciones' => $items,
    ]);
}

}
