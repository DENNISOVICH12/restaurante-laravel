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

    /** @OA\Post(path="/api/clientes", operationId="ClientesStore", summary="Crear cliente", tags={"Clientes"}, @OA\RequestBody(required=true, @OA\JsonContent(type="object", required={"nombre_cliente","telefono"}, @OA\Property(property="nombre_cliente", type="string", example="Juan Pérez"), @OA\Property(property="telefono", type="string", example="3001234567"), @OA\Property(property="direccion", type="string", example="Calle 10 #5-20"))), @OA\Response(response=201, description="Creado"), @OA\Response(response=422, description="Datos inválidos")) */







    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre_cliente'=>'required|string|max:255',
            'telefono'      =>'nullable|string|max:50',
            'direccion'     =>'nullable|string|max:255',
        ]);
        if (schema()->hasColumn('clientes','fecha_registro')) {
            $data['fecha_registro'] = now();
        }
        $c = Cliente::create($data);
        return $this->created('Cliente creado', $c);
    }

    /** @OA\Put(path="/api/clientes/{id}", operationId="ClientesUpdate", summary="Actualizar cliente", tags={"Clientes"}, @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")), @OA\RequestBody(@OA\JsonContent(type="object", @OA\Property(property="nombre_cliente", type="string"), @OA\Property(property="telefono", type="string"), @OA\Property(property="direccion", type="string"))), @OA\Response(response=200, description="OK"), @OA\Response(response=404, description="No encontrado"), @OA\Response(response=422, description="Datos inválidos")) */

    public function update(Request $request, int $id): JsonResponse
    {
        $c = Cliente::find($id);
        if (!$c) return $this->notFound();

        $data = $request->validate([
            'nombre_cliente'=>'sometimes|required|string|max:255',
            'telefono'      =>'sometimes|nullable|string|max:50',
            'direccion'     =>'sometimes|nullable|string|max:255',
        ]);

        $c->update($data);
        return $this->okData('Cliente actualizado', $c);
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
