<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MenuItemController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/menu-items",
     *   tags={"Menu Items"},
     *   summary="Listar ítems del menú (paginado)",
     *   @OA\Response(response=200, description="OK",
     *     @OA\JsonContent(type="object",
     *       @OA\Property(property="data", type="array",
     *         @OA\Items(
     *           @OA\Property(property="id", type="integer", example=1),
     *           @OA\Property(property="nombre", type="string", example="Limonada"),
     *           @OA\Property(property="descripcion", type="string", nullable=true, example="Natural"),
     *           @OA\Property(property="categoria", type="string", example="bebida"),
     *           @OA\Property(property="precio", type="number", format="float", example=6.5),
     *           @OA\Property(property="imagen", type="string", nullable=true, example="limonada.jpg"),
     *           @OA\Property(property="disponible", type="boolean", example=true)
     *         )
     *       )
     *     )
     *   )
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
     *   @OA\RequestBody(required=true,
     *     @OA\JsonContent(required={"nombre","categoria","precio"},
     *       @OA\Property(property="nombre", type="string", example="Limonada"),
     *       @OA\Property(property="descripcion", type="string", nullable=true, example="Natural"),
     *       @OA\Property(property="categoria", type="string", example="bebida"),
     *       @OA\Property(property="precio", type="number", format="float", example=6.5),
     *       @OA\Property(property="imagen", type="string", nullable=true, example="limonada.jpg"),
     *       @OA\Property(property="disponible", type="boolean", example=true)
     *     )
     *   ),
     *   @OA\Response(response=201, description="Creado"),
     *   @OA\Response(response=422, description="Validación fallida")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => ['required','string','max:120'],
            'descripcion' => ['nullable','string'],
            'categoria'   => ['required','string'], // ej: 'plato' | 'bebida'
            'precio'      => ['required','numeric'],
            'imagen'      => ['nullable','string'],
            'disponible'  => ['boolean'],
        ]);

        $item = MenuItem::create($data);
        return response()->json($item, 201);
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
     *       @OA\Property(property="disponible", type="boolean", example=false)
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
            'nombre'      => ['sometimes','string','max:120'],
            'descripcion' => ['sometimes','nullable','string'],
            'categoria'   => ['sometimes','string'],
            'precio'      => ['sometimes','numeric'],
            'imagen'      => ['sometimes','nullable','string'],
            'disponible'  => ['sometimes','boolean'],
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
