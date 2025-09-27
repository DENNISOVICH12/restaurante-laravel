<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   schema="Usuario",
 *   type="object",
 *   required={"id","usuario","nombre","correo","rol","restaurant_id"},
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="usuario", type="string", example="jadmin"),
 *   @OA\Property(property="nombre", type="string", example="Juan"),
 *   @OA\Property(property="apellido", type="string", nullable=true, example="Admin"),
 *   @OA\Property(property="correo", type="string", format="email", example="jadmin@ejemplo.com"),
 *   @OA\Property(property="rol", type="string", example="admin"),
 *   @OA\Property(property="activo", type="boolean", example=true),
 *   @OA\Property(property="restaurant_id", type="integer", example=1),
 *   @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-27T10:00:00Z"),
 *   @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-27T10:00:00Z")
 * )
 */
class Usuario extends Model
{
    protected $table = 'usuarios';

    protected $fillable = [
        'usuario', 'password', 'nombre', 'apellido', 'correo',
        'rol', 'activo', 'restaurant_id',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    protected $hidden = ['password'];

    // Hasheado automático: al asignar "password" se guarda con bcrypt
    public function setPasswordAttribute($value): void
    {
        if (!empty($value)) {
            $this->attributes['password'] = bcrypt($value);
        }
    }
}
