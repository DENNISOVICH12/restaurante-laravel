<?php
namespace App\Http\Controllers;

use App\Models\Bebida;

class BebidaTableController extends Controller
{
    /** @OA\Get(
     *   path="/api/bebidas-fisicas",
     *   summary="Listado de bebidas (tabla física)",
     *   tags={"Bebidas (tabla)"},
     *   @OA\Response(response=200, description="OK")
     * ) */
    public function index() {
        $rows = Bebida::orderBy('id','desc')->get();
        return response()->json(['message'=>'Listado de bebidas (tabla)', 'data'=>$rows], 200);
    }
}
