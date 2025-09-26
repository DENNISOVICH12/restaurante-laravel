<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/ping",
     *   summary="Prueba de vida",
     *   tags={"Health"},
     *   @OA\Response(response=200, description="El API está vivo")
     * )
     */
    public function ping(): JsonResponse
    {
        return response()->json(['pong' => true]);
    }
}
