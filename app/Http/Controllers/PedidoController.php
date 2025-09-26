<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\MenuItem;
use App\Models\Plato;
use App\Models\Bebida;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PedidoController extends Controller
{
    private function ok(string $message, array $extra = []) { return response()->json(array_merge(['message'=>$message], $extra), 200); }
    private function okData(string $message, $data, array $extra = []) { return response()->json(array_merge(['message'=>$message,'data'=>$data], $extra), 200); }
    private function created(string $message, $data) { return response()->json(['message'=>$message,'data'=>$data], 201); }
    private function notFound() { return response()->json(['error'=>['code'=>404,'message'=>'No encontrado']], 404); }

    /**
     * @OA\Get(
     *   path="/api/pedidos",
     *   operationId="PedidosIndex",
     *   summary="Lista de pedidos",
     *   tags={"Pedidos"},
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function index(Request $request)
    {
        $q = Pedido::with('cliente')->orderBy('id','desc');
        if ($e = $request->query('estado'))     $q->where('estado',$e);
        if ($c = $request->query('id_cliente')) $q->where('id_cliente',$c);

        $p = $q->paginate(10);
        $meta = [
            'current_page'=>$p->currentPage(),
            'per_page'    =>$p->perPage(),
            'total'       =>$p->total(),
            'last_page'   =>$p->lastPage(),
        ];
        return $this->okData('Listado de pedidos', $p->items(), ['meta'=>$meta]);
    }

    /**
     * @OA\Get(
     *   path="/api/pedidos/{id}",
     *   operationId="PedidosShow",
     *   summary="Ver pedido por ID",
     *   tags={"Pedidos"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $pedido = Pedido::with(['cliente','detalle'])->find($id);
        if (!$pedido) return $this->notFound();
        return $this->okData('Pedido encontrado', $pedido);
    }

    /**
     * @OA\Post(
     *   path="/api/pedidos",
     *   summary="Crear pedido con items",
     *   tags={"Pedidos"},
     *   @OA\RequestBody(required=true,@OA\JsonContent(
     *     required={"id_cliente","items"},
     *     @OA\Property(property="id_cliente",type="integer",example=1),
     *     @OA\Property(property="mesa",type="string",example="A3"),
     *     @OA\Property(property="estado",type="string",example="pendiente"),
     *     @OA\Property(property="items",type="array",
     *       @OA\Items(
     *         @OA\Property(property="id_menu_item",type="integer",example=2),
     *         @OA\Property(property="nombre_producto",type="string",example="Limonada"),
     *         @OA\Property(property="precio",type="number",format="float",example=5.5),
     *         @OA\Property(property="categoria",type="string",example="bebida"),
     *         @OA\Property(property="cantidad",type="integer",example=2),
     *         @OA\Property(property="descripcion",type="string",example="sin azúcar")
     *       )
     *     )
     *   )),
     *   @OA\Response(response=201, description="Creado"),
     *   @OA\Response(response=422, description="Datos inválidos")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_cliente' => 'required|integer|exists:clientes,id',
            'mesa'       => 'nullable|string|max:50',
            'estado'     => 'nullable|in:pendiente,en_entrega,listo,entregado,cancelado',
            'items'      => 'required|array|min:1',
            'items.*.id_menu_item'    => 'nullable|integer|exists:menu_items,id',
            'items.*.nombre_producto' => 'required_without:items.*.id_menu_item|string|max:100',
            'items.*.precio'          => 'required_without:items.*.id_menu_item|numeric|min:0',
            'items.*.categoria'       => 'required_without:items.*.id_menu_item|string|max:50',
            'items.*.cantidad'        => 'required|integer|min:1',
            'items.*.descripcion'     => 'nullable|string',
        ]);

        $pedido = Pedido::create([
            'id_cliente' => $data['id_cliente'],
            'mesa'       => $data['mesa'] ?? null,
            'estado'     => $data['estado'] ?? 'pendiente',
            'fecha'      => now(),
        ]);

        foreach ($data['items'] as $it) {
            // 1) Resolver datos del item
            $nombre      = $it['nombre_producto'] ?? null;
            $precio      = $it['precio'] ?? null;
            $categoria   = $it['categoria'] ?? null;
            $descripcion = $it['descripcion'] ?? null;

            if (!empty($it['id_menu_item'])) {
                $mi = MenuItem::find($it['id_menu_item']);
                if ($mi) {
                    $nombre      = $mi->nombre;
                    $precio      = $mi->precio;
                    $categoria   = $mi->categoria;
                    $descripcion = $descripcion ?? $mi->descripcion;
                }
            }

            // 2) Crear detalle del pedido
            $detalle = DetallePedido::create([
                'id_pedido'       => $pedido->id,
                'nombre_producto' => $nombre,
                'precio'          => $precio,
                'cantidad'        => (int)$it['cantidad'],
                'categoria'       => $categoria,
                'descripcion'     => $descripcion,
            ]);

            // 3) Poblar tablas físicas según la categoría (opcional)
            $cat = strtolower((string)$categoria);
            if ($cat === 'plato') {
                Plato::create([
                    'id_pedido'       => $pedido->id,
                    'id_detalle'      => $detalle->id ?? null,
                    'nombre_producto' => $nombre,
                    'precio'          => $precio,
                    'cantidad'        => (int)$it['cantidad'],
                    'descripcion'     => $descripcion,
                ]);
            } elseif ($cat === 'bebida') {
                Bebida::create([
                    'id_pedido'       => $pedido->id,
                    'id_detalle'      => $detalle->id ?? null,
                    'nombre_producto' => $nombre,
                    'precio'          => $precio,
                    'cantidad'        => (int)$it['cantidad'],
                    'descripcion'     => $descripcion,
                ]);
            }
        }

        $pedido = Pedido::with(['cliente','detalle'])->find($pedido->id);
        return $this->created('Pedido creado', $pedido);
    }

    /**
     * @OA\Put(
     *   path="/api/pedidos/{id}",
     *   summary="Actualizar pedido (mesa/estado)",
     *   tags={"Pedidos"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(@OA\JsonContent(
     *     @OA\Property(property="mesa",type="string"),
     *     @OA\Property(property="estado",type="string",enum={"pendiente","en_entrega","listo","entregado","cancelado"})
     *   )),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=404, description="No encontrado"),
     *   @OA\Response(response=422, description="Datos inválidos")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $pedido = Pedido::find($id);
        if (!$pedido) return $this->notFound();

        $data = $request->validate([
            'mesa'   => 'sometimes|nullable|string|max:50',
            'estado' => 'sometimes|in:pendiente,en_entrega,listo,entregado,cancelado',
        ]);

        $pedido->fill($data)->save();
        $pedido = Pedido::with(['cliente','detalle'])->find($id);
        return $this->okData('Pedido actualizado', $pedido);
    }

    /**
     * @OA\Delete(
     *   path="/api/pedidos/{id}",
     *   summary="Eliminar pedido",
     *   tags={"Pedidos"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Eliminado"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $pedido = Pedido::find($id);
        if (!$pedido) return $this->notFound();

        DetallePedido::where('id_pedido',$id)->delete();
        $pedido->delete();
        return $this->ok('Pedido eliminado');
    }

    /**
     * @OA\Get(
     *   path="/api/pedidos/{id}/detalle",
     *   summary="Detalle del pedido",
     *   tags={"Pedidos - Detalle"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function detalle(int $id): JsonResponse
    {
        if (!Pedido::find($id)) return $this->notFound();
        $items = DetallePedido::where('id_pedido',$id)->get();
        return $this->okData('Detalle listado', $items);
    }
}
