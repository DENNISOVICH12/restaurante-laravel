<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';

    protected $fillable = [
        'usuario', 'password', 'nombre', 'apellido', 'correo', 'activo', 'rol','restaurant_id'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    protected $hidden = ['password'];

    // Si NO quieres timestamps en la tabla, descomenta:
    // public $timestamps = false;

    // Hash automático del password
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = bcrypt($value);
        }
    }
}
