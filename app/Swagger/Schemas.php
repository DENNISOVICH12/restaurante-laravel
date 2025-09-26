<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *   schema="Usuario",
 *   type="object",
 *   required={"usuario","password","nombre","correo"},
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="usuario", type="string", example="admin"),
 *   @OA\Property(property="password", type="string", example="secreto123"),
 *   @OA\Property(property="nombre", type="string", example="Admin"),
 *   @OA\Property(property="apellido", type="string", nullable=true, example="Demo"),
 *   @OA\Property(property="correo", type="string", format="email", example="admin@example.com"),
 *   @OA\Property(property="activo", type="boolean", example=true),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Schemas {}
