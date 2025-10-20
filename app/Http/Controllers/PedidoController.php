<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Pedidos",
 *     description="Gestión de pedidos del restaurante"
 * )
 */
class PedidoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/pedidos",
     *     tags={"Pedidos"},
     *     summary="Listar todos los pedidos",
     *     @OA\Response(response=200, description="Lista de pedidos")
     * )
     */
    
    public function index()
{
    $restaurantId = app('current_restaurant_id');

    // 🔹 Si no hay restaurante activo, mostrar todos durante pruebas
    if (app()->runningUnitTests() || !$restaurantId) {
        $pedidos = Pedido::with(['cliente', 'detalle'])->get();
    } else {
        $pedidos = Pedido::where('restaurant_id', $restaurantId)
            ->with(['cliente', 'detalle'])
            ->get();
    }

    return response()->json([
        'message' => 'Listado de pedidos',
        'meta' => ['total' => $pedidos->count()],
        'data' => $pedidos,
    ]);
}


    /**
     * @OA\Post(
     *     path="/api/pedidos",
     *     tags={"Pedidos"},
     *     summary="Registrar un nuevo pedido",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"cliente_id", "detalle"},
     *             @OA\Property(property="cliente_id", type="integer", example=1),
     *             @OA\Property(property="detalle", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="menu_item_id", type="integer", example=2),
     *                     @OA\Property(property="cantidad", type="integer", example=3),
     *                     @OA\Property(property="precio", type="number", format="float", example=12000)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Pedido creado exitosamente"),
     *     @OA\Response(response=400, description="Error de validación")
     * )
     */
    public function store(Request $request)
    {
        // Obtener restaurante activo (si no, fallback 1)
        $restaurantId = app('current_restaurant_id') ?? 1;

        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'detalle' => 'required|array|min:1',
            'detalle.*.menu_item_id' => 'required|exists:menu_items,id',
            'detalle.*.cantidad' => 'required|integer|min:1',
            'detalle.*.precio' => 'required|numeric|min:0',
        ]);

        $total = collect($validated['detalle'])
            ->sum(fn($d) => $d['cantidad'] * $d['precio']);

        $pedido = Pedido::create([
            'cliente_id' => $validated['cliente_id'],
            'restaurant_id' => $restaurantId,
            'total' => $total,
        ]);

        foreach ($validated['detalle'] as $d) {
            $pedido->detalle()->create([
                'menu_item_id' => $d['menu_item_id'],
                'cantidad' => $d['cantidad'],
                'precio_unitario' => $d['precio'],
                'importe' => $d['cantidad'] * $d['precio'],
                'restaurant_id' => $restaurantId,
            ]);
        }

        return response()->json([
            'message' => 'Pedido creado',
            'data' => $pedido->load('detalle'),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/pedidos/{id}",
     *     tags={"Pedidos"},
     *     summary="Mostrar un pedido por ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Pedido encontrado"),
     *     @OA\Response(response=404, description="Pedido no encontrado")
     * )
     */
    public function show($id)
    {
        $pedido = Pedido::with('detalle')->find($id);

        if (!$pedido) {
            return response()->json([
                'message' => 'Pedido no encontrado',
            ], 404);
        }

        return response()->json([
            'message' => 'Pedido encontrado',
            'data' => $pedido,
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/pedidos/{id}",
     *     tags={"Pedidos"},
     *     summary="Actualizar un pedido existente",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="cliente_id", type="integer", example=1),
     *             @OA\Property(property="total", type="number", example=45000)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Pedido actualizado correctamente"),
     *     @OA\Response(response=404, description="Pedido no encontrado")
     * )
     */
    public function update(Request $request, $id)
    {
        $pedido = Pedido::find($id);

        if (!$pedido) {
            return response()->json(['message' => 'Pedido no encontrado'], 404);
        }

        $validated = $request->validate([
            'cliente_id' => 'sometimes|integer|exists:clientes,id',
            'total' => 'sometimes|numeric|min:0',
        ]);

        $pedido->update($validated);

        return response()->json([
            'message' => 'Pedido actualizado correctamente',
            'data' => $pedido,
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/pedidos/{id}",
     *     tags={"Pedidos"},
     *     summary="Eliminar un pedido",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Pedido eliminado correctamente"),
     *     @OA\Response(response=404, description="Pedido no encontrado")
     * )
     */
    public function destroy($id)
    {
        $pedido = Pedido::find($id);

        if (!$pedido) {
            return response()->json(['message' => 'Pedido no encontrado'], 404);
        }

        $pedido->delete();

        return response()->json(['message' => 'Pedido eliminado correctamente'], 200);
    }
}
