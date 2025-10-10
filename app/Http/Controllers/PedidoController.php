<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Http\Requests\PedidoStoreRequest; 

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;


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
     *   @OA\Parameter(
     *     name="X-Restaurant-ID",
     *     in="header",
     *     required=false,
     *     description="ID numérico o slug del restaurante para filtrar el contexto",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Listado paginado",
     *     @OA\JsonContent(ref="#/components/schemas/PedidoPaginatedResponse")
     *   )
     * )
     */
    public function index(Request $request)
    {
        $q = Pedido::query()
            ->with(['cliente' => function ($builder) {
                $builder->select('id', 'nombre_cliente', 'telefono', 'direccion');
            }])
            ->withSum('detalle as importe_total', 'importe');

        if ($estado = $request->query('estado')) {
            $q->where('estado', $estado);
        }

        $perPage = (int) $request->query('per_page', 10);
        if ($perPage <= 0) {
            $perPage = 10;
        }

        $p = $q->orderByDesc('created_at')->paginate($perPage);
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
     *   @OA\Parameter(
     *     name="X-Restaurant-ID",
     *     in="header",
     *     required=false,
     *     description="ID numérico o slug del restaurante para filtrar el contexto",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Pedido con detalle",
     *     @OA\JsonContent(ref="#/components/schemas/PedidoDetailResponse")
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="No encontrado",
     *     @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *   )
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
     *   @OA\Parameter(
     *     name="X-Restaurant-ID",
     *     in="header",
     *     required=false,
     *     description="ID numérico o slug del restaurante. Si no se envía y solo hay un restaurante, se usará automáticamente.",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/PedidoCreateRequest")
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Pedido creado",
     *     @OA\JsonContent(ref="#/components/schemas/PedidoDetailResponse")
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Datos inválidos"
     *   )
     * )
     */
    public function store(PedidoStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        $pedido = DB::transaction(function () use ($data) {
            $pedido = Pedido::create([
                'cliente_id'    => $data['cliente_id'],
                'restaurant_id' => $data['restaurant_id'],
                'mesa'          => $data['mesa'] ?? null,
                'estado'        => $data['estado'] ?? 'pendiente',

            ]);

            foreach ($data['items'] as $item) {
                $precioUnit = (float) $item['precio'];
                $cant       = (int) $item['cantidad'];

                \App\Models\DetallePedido::create([
                    'pedido_id'      => $pedido->id,
                    'restaurant_id'  => $data['restaurant_id'],
                    'menu_item_id'   => $item['menu_item_id'] ?? null,
                    'cantidad'       => $cant,
                    'precio_unitario'=> $precioUnit,
                    'importe'        => $precioUnit * $cant,
                ]);
            }

             return $pedido;
        });

        $pedido->load(['cliente', 'detalle']);

        return $this->created('Pedido creado', $pedido);
    }

    /**
     * @OA\Put(
     *   path="/api/pedidos/{id}",
     *   summary="Actualizar pedido (estado)",
     *   tags={"Pedidos"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Parameter(
     *     name="X-Restaurant-ID",
     *     in="header",
     *     required=false,
     *     description="ID numérico o slug del restaurante para el que se procesa el pedido",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/PedidoUpdateRequest")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Pedido actualizado",
     *     @OA\JsonContent(ref="#/components/schemas/PedidoDetailResponse")
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="No encontrado",
     *     @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Datos inválidos"
     *   )
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
     *   @OA\Parameter(
     *     name="X-Restaurant-ID",
     *     in="header",
     *     required=false,
     *     description="ID numérico o slug del restaurante para asegurar el contexto",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Eliminado",
     *     @OA\JsonContent(ref="#/components/schemas/ApiMessageResponse")
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="No encontrado",
     *     @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *   )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $pedido = Pedido::find($id);
        if (!$pedido) return $this->notFound();

        DB::transaction(function () use ($pedido) {
            $pedido->detalle()->delete();
            $pedido->delete();
        });
        return $this->ok('Pedido eliminado');
    }

    /**
     * @OA\Get(
     *   path="/api/pedidos/{id}/detalle",
     *   summary="Detalle del pedido",
     *   tags={"Pedidos - Detalle"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Parameter(
     *     name="X-Restaurant-ID",
     *     in="header",
     *     required=false,
     *     description="ID numérico o slug del restaurante",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Detalle listado",
     *     @OA\JsonContent(ref="#/components/schemas/PedidoItemsResponse")
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="No encontrado",
     *     @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *   )
     * )
     */
    public function detalle(int $id): JsonResponse
    {
        if (!Pedido::find($id)) return $this->notFound();
        $items = DetallePedido::where('pedido_id',$id)->get();
        return $this->okData('Detalle listado', $items);
    }
}