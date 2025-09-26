<?php
namespace App\Http\Controllers;

use App\Models\Plato;

class PlatoTableController extends Controller
{
    /** @OA\Get(
     *   path="/api/platos-fisicos",
     *   summary="Listado de platos (tabla física)",
     *   tags={"Platos (tabla)"},
     *   @OA\Response(response=200, description="OK")
     * ) */
    public function index() {
        $rows = Plato::orderBy('id','desc')->get();
        return response()->json(['message'=>'Listado de platos (tabla)', 'data'=>$rows], 200);
    }
}
