<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // <—
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable // <—
{
    use Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'usuario', 'password', 'nombre', 'apellido', 'correo', 'rol', 'activo', 'restaurant_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['activo' => 'boolean'];

    // si tu login usa el campo "usuario", no "email"
    public function getAuthIdentifierName()
    {
        return 'usuario';
    }

    // Hash automático del password
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = bcrypt($value);
        }
    }
}