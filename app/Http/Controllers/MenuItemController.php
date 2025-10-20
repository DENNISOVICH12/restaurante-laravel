<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuItemController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/menu-items",
     *   tags={"Menu Items"},
     *   summary="Listar ítems del menú (paginado)",
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function index()
    {
        return response()->json(MenuItem::paginate(20));
    }

    /**
     * @OA\Post(
     *   path="/api/menu-items",
     *   tags={"Menu Items"},
     *   summary="Crear ítem de menú",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"nombre","categoria","precio"},
     *       @OA\Property(property="nombre", type="string", example="Limonada"),
     *       @OA\Property(property="descripcion", type="string", nullable=true, example="Natural"),
     *       @OA\Property(property="categoria", type="string", example="bebida"),
     *       @OA\Property(property="precio", type="number", format="float", example=6.5),
     *       @OA\Property(property="imagen", type="string", nullable=true, example="limonada.jpg"),
     *       @OA\Property(property="disponible", type="boolean", example=true),
     *       @OA\Property(property="restaurant_id", type="integer", example=1)
     *     )
     *   ),
     *   @OA\Response(response=201, description="Creado"),
     *   @OA\Response(response=422, description="Validación fallida")
     * )
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nombre'        => ['required','string','max:120'],
                'descripcion'   => ['nullable','string'],
                'categoria'     => ['required','string'],
                'precio'        => ['required','numeric'],
                'imagen'        => ['nullable','string'],
                'disponible'    => ['boolean'],
                'restaurant_id' => ['nullable','integer'],
            ]);

            $data['disponible']    = filter_var($data['disponible'] ?? true, FILTER_VALIDATE_BOOLEAN);
            $data['restaurant_id'] = $data['restaurant_id'] ?? 1;

            $item = MenuItem::create($data);
            return response()->json($item, 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el ítem',
                'error'   => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Post(
     *   path="/api/menu-items/bulk",
     *   tags={"Menu Items"},
     *   summary="Crear múltiples ítems del menú (bulk)",
     *   description="Crear varios ítems enviando un objeto con la propiedad 'items' (array de objetos).",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *           type="object",
     *           @OA\Property(
     *               property="items",
     *               type="array",
     *               @OA\Items(
     *                   type="object",
     *                   required={"nombre","categoria","precio"},
     *                   @OA\Property(property="nombre", type="string", example="Limonada"),
     *                   @OA\Property(property="descripcion", type="string", nullable=true, example="Natural"),
     *                   @OA\Property(property="categoria", type="string", example="bebida"),
     *                   @OA\Property(property="precio", type="number", format="float", example=6.5),
     *                   @OA\Property(property="imagen", type="string", nullable=true, example="limonada.jpg"),
     *                   @OA\Property(property="disponible", type="boolean", example=true),
     *                   @OA\Property(property="restaurant_id", type="integer", example=1)
     *               )
     *           )
     *       )
     *   ),
     *   @OA\Response(response=201, description="Ítems creados correctamente"),
     *   @OA\Response(response=422, description="Validación fallida")
     * )
     */
    public function storeBulk(Request $request)
    {
        // Acepta { "items": [...] } o array plano [...]
        $payload = $request->input('items') ?? $request->json()->all();

        // Si el JSON raíz es un objeto con 'items', $payload será array; si es un array plano, también OK.
        if (!is_array($payload)) {
            return response()->json(['message' => 'Formato JSON inválido. Se esperaba un array o un objeto con "items".'], 422);
        }

        // Si el cliente envió un objeto completo (no 'items'), intentar normalizar:
        if (array_key_exists('items', $request->all()) && is_array($request->all()['items'])) {
            $payload = $request->all()['items'];
        }

        $dataWrap = ['items' => $payload];

        $validator = Validator::make($dataWrap, [
            'items'                 => ['required','array','min:1'],
            'items.*.nombre'        => ['required','string','max:120'],
            'items.*.descripcion'   => ['nullable','string'],
            'items.*.categoria'     => ['required','string'],
            'items.*.precio'        => ['sometimes','numeric','min:0'],
            'items.*.imagen'        => ['nullable','string'],
            'items.*.disponible'    => ['boolean'],
            'items.*.restaurant_id' => ['nullable','integer'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $created = [];
        foreach ($dataWrap['items'] as $row) {
            $row['disponible']    = filter_var($row['disponible'] ?? true, FILTER_VALIDATE_BOOLEAN);
            $row['restaurant_id'] = $row['restaurant_id'] ?? 1;
            $created[] = MenuItem::create($row);
        }

        return response()->json([
            'message' => 'Ítems creados correctamente',
            'count'   => count($created),
            'data'    => $created,
        ], 201);
    }

    /**
     * @OA\Get(
     *   path="/api/menu-items/{id}",
     *   tags={"Menu Items"},
     *   summary="Ver ítem",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function show($id)
    {
        return response()->json(MenuItem::findOrFail($id));
    }

    /**
     * @OA\Put(
     *   path="/api/menu-items/{id}",
     *   tags={"Menu Items"},
     *   summary="Actualizar ítem",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(required=true,
     *     @OA\JsonContent(
     *       @OA\Property(property="nombre", type="string", example="Limonada natural"),
     *       @OA\Property(property="descripcion", type="string", nullable=true, example="Con hierbabuena"),
     *       @OA\Property(property="categoria", type="string", example="bebida"),
     *       @OA\Property(property="precio", type="number", format="float", example=7.0),
     *       @OA\Property(property="imagen", type="string", nullable=true, example="limonada_2.jpg"),
     *       @OA\Property(property="disponible", type="boolean", example=false),
     *       @OA\Property(property="restaurant_id", type="integer", example=1)
     *     )
     *   ),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=404, description="No encontrado"),
     *   @OA\Response(response=422, description="Validación fallida")
     * )
     */
    public function update(Request $request, $id)
    {
        $item = MenuItem::findOrFail($id);

        $data = $request->validate([
            'nombre'        => ['sometimes','string','max:120'],
            'descripcion'   => ['sometimes','nullable','string'],
            'categoria'     => ['sometimes','string'],
            'precio'        => ['sometimes','numeric','min:1'],
            'imagen'        => ['sometimes','nullable','string'],
            'disponible'    => ['sometimes','boolean'],
            'restaurant_id' => ['sometimes','integer'],
        ]);

        $item->update($data);
        return response()->json($item);
    }

    /**
     * @OA\Delete(
     *   path="/api/menu-items/{id}",
     *   tags={"Menu Items"},
     *   summary="Eliminar ítem",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Eliminado"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function destroy($id)
    {
        MenuItem::findOrFail($id)->delete();
        return response()->json(['deleted' => true]);
    }
}
