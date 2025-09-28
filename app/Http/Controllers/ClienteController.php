<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClienteController extends Controller
{
    // Helpers de respuesta con mensaje
    private function ok(string $message, array $extra = []) {
        return response()->json(array_merge(['message'=>$message], $extra), 200);
    }
    private function okData(string $message, $data, array $extra = []) {
        return response()->json(array_merge(['message'=>$message,'data'=>$data], $extra), 200);
    }
    private function created(string $message, $data) {
        return response()->json(['message'=>$message,'data'=>$data], 201);
    }
    private function notFound() {
        return response()->json(['error'=>['code'=>404,'message'=>'No encontrado']], 404);
    }

    /**
     * @OA\Get(
     *   path="/api/clientes",
     *   operationId="ClientesIndex",
     *   summary="Lista de clientes",
     *   tags={"Clientes"},
     *   @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *   @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", default=1)),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=500, description="Error del servidor")
     * )
     */
    public function index(Request $request)
    {
        $q = Cliente::query();
        if ($s = $request->query('search')) {
            $q->where('nombre_cliente','like',"%$s%");
        }
        $p = $q->orderBy('id','desc')->paginate(10);

        $meta = [
            'current_page'=>$p->currentPage(),
            'per_page'    =>$p->perPage(),
            'total'       =>$p->total(),
            'last_page'   =>$p->lastPage(),
        ];

        return $this->okData('Listado de clientes', $p->items(), ['meta'=>$meta]);
    }

    /**
     * @OA\Get(
     *   path="/api/clientes/{id}",
     *   operationId="ClientesShow",
     *   summary="Ver cliente",
     *   tags={"Clientes"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $c = Cliente::find($id);
        if (!$c) return $this->notFound();
        return $this->okData('Cliente encontrado', $c);
    }

    /** @OA\Post(
 *   path="/api/clientes",
 *   tags={"Clientes"},
 *   summary="Crear cliente",
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\JsonContent(
 *       required={"nombre_cliente"},
 *       @OA\Property(property="nombre_cliente", type="string", example="Juan Pérez"),
 *       @OA\Property(property="telefono", type="string", example="3001234567"),
 *       @OA\Property(property="direccion", type="string", example="Calle 10 #5-20"),
 *       @OA\Property(property="restaurant_id", type="integer", example=1)
 *     )
 *   ),
 *   @OA\Response(response=201, description="Creado"),
 *   @OA\Response(response=422, description="Datos inválidos")
 * )
 */


    public function store(Request $request): JsonResponse
{
    $data = $request->validate([
        'nombre' => 'required|string|max:255',
        'email'  => 'nullable|email|max:255',
        'restaurant_id' => 'required|integer|exists:restaurants,id',
    ]);

    $cliente = Cliente::create($data);
    return $this->created('Cliente creado correctamente', $cliente);
}


    /** @OA\Put(path="/api/clientes/{id}", operationId="ClientesUpdate", summary="Actualizar cliente", tags={"Clientes"}, @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")), @OA\RequestBody(@OA\JsonContent(type="object", @OA\Property(property="nombre_cliente", type="string"), @OA\Property(property="telefono", type="string"), @OA\Property(property="direccion", type="string"))), @OA\Response(response=200, description="OK"), @OA\Response(response=404, description="No encontrado"), @OA\Response(response=422, description="Datos inválidos")) */

    public function update(Request $request, int $id): JsonResponse
{
    $cliente = Cliente::find($id);
    if (!$cliente) return $this->notFound();

    $data = $request->validate([
        'nombre' => 'sometimes|required|string|max:255',
        'email'  => 'sometimes|nullable|email|max:255',
        'restaurant_id' => 'sometimes|required|integer|exists:restaurants,id',
    ]);

    $cliente->update($data);
    return $this->okData('Cliente actualizado correctamente', $cliente);
}
    /** @OA\Delete(path="/api/clientes/{id}", operationId="ClientesDestroy", summary="Eliminar cliente", tags={"Clientes"}, @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")), @OA\Response(response=200, description="Eliminado"), @OA\Response(response=404, description="No encontrado")) */


    public function destroy(int $id): JsonResponse
    {
        $c = Cliente::find($id);
        if (!$c) return $this->notFound();

        $c->delete();
        return $this->ok('Cliente eliminado');
    }
}

// helper schema()
if (!function_exists('schema')) {
    function schema() { return app('db')->connection()->getSchemaBuilder(); }
}
