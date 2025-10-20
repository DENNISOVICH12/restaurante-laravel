<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClienteController extends Controller
{
    // --- Helpers para respuestas JSON ---
    private function ok(string $message, array $extra = []) {
        return response()->json(array_merge(['message' => $message], $extra), 200);
    }

    private function okData(string $message, $data, array $extra = []) {
        return response()->json(array_merge(['message' => $message, 'data' => $data], $extra), 200);
    }

    private function created(string $message, $data) {
        return response()->json(['message' => $message, 'data' => $data], 201);
    }

    private function notFound() {
        return response()->json(['error' => ['code' => 404, 'message' => 'No encontrado']], 404);
    }

    // --- GET: Listar clientes ---
    /**
     * @OA\Get(
     *   path="/api/clientes",
     *   tags={"Clientes"},
     *   summary="Listar todos los clientes",
     *   @OA\Response(response=200, description="Lista de clientes")
     * )
     */
    public function index(Request $request)
    {
        $query = Cliente::query();

        // Búsqueda opcional por nombre
        if ($search = $request->query('search')) {
            $query->where('nombre_cliente', 'like', "%$search%");
        }

        $clientes = $query->orderBy('id', 'desc')->paginate(10);

        $meta = [
            'current_page' => $clientes->currentPage(),
            'per_page'     => $clientes->perPage(),
            'total'        => $clientes->total(),
            'last_page'    => $clientes->lastPage(),
        ];

        return $this->okData('Listado de clientes', $clientes->items(), ['meta' => $meta]);
    }

    // --- GET: Mostrar un cliente por ID ---
    /**
     * @OA\Get(
     *   path="/api/clientes/{id}",
     *   tags={"Clientes"},
     *   summary="Mostrar un cliente específico",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Cliente encontrado"),
     *   @OA\Response(response=404, description="Cliente no encontrado")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $cliente = Cliente::find($id);
        if (!$cliente) return $this->notFound();

        return $this->okData('Cliente encontrado', $cliente);
    }

    // --- POST: Crear cliente ---
    /**
     * @OA\Post(
     *   path="/api/clientes",
     *   tags={"Clientes"},
     *   summary="Registrar un nuevo cliente",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"nombre_cliente"},
     *       @OA\Property(property="nombre_cliente", type="string", example="Juan Pérez"),
     *       @OA\Property(property="telefono", type="string", example="3001234567"),
     *       @OA\Property(property="direccion", type="string", example="Calle 10 #5-20")
     *     )
     *   ),
     *   @OA\Response(response=201, description="Cliente creado exitosamente"),
     *   @OA\Response(response=422, description="Datos inválidos")
     * )
     */
    public function store(Request $request): JsonResponse
{
    $data = $request->validate([
        'nombre_cliente' => 'required|string|max:255|unique:clientes,nombre_cliente',
        'telefono' => 'nullable|digits_between:7,15',
        'direccion' => 'nullable|string|max:255',
    ]);

    $cliente = Cliente::create($data);
    return $this->created('Cliente creado correctamente', $cliente);
}

    // --- PUT: Actualizar cliente ---
    /**
     * @OA\Put(
     *   path="/api/clientes/{id}",
     *   tags={"Clientes"},
     *   summary="Actualizar datos de un cliente",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       @OA\Property(property="nombre_cliente", type="string", example="Carlos Gómez"),
     *       @OA\Property(property="telefono", type="string", example="3105559999"),
     *       @OA\Property(property="direccion", type="string", example="Av. Siempre Viva 742")
     *     )
     *   ),
     *   @OA\Response(response=200, description="Cliente actualizado correctamente"),
     *   @OA\Response(response=404, description="Cliente no encontrado")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
{
    $cliente = Cliente::find($id);
    if (!$cliente) return $this->notFound();

    $data = $request->validate([
        'nombre_cliente' => 'sometimes|required|string|max:255',
        'telefono' => 'nullable|digits_between:7,15',
        'direccion' => 'sometimes|nullable|string|max:255',
    ]);

    $cliente->update($data);
    return $this->okData('Cliente actualizado correctamente', $cliente);
}

    // --- DELETE: Eliminar cliente ---
    /**
     * @OA\Delete(
     *   path="/api/clientes/{id}",
     *   tags={"Clientes"},
     *   summary="Eliminar un cliente",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Cliente eliminado correctamente"),
     *   @OA\Response(response=404, description="Cliente no encontrado")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $cliente = Cliente::find($id);
        if (!$cliente) return $this->notFound();

        if ($cliente->pedidos()->count() > 0) {
        return response()->json([
            'error' => 'No se puede eliminar un cliente con pedidos asociados'
        ], 409);
    }

        $cliente->delete();
        return $this->ok('Cliente eliminado correctamente');
    }
}
