<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    // Tu tabla real
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    public $timestamps = false; // si tu tabla no tiene created_at/updated_at

    protected $fillable = ['nombre','correo','password','rol','activo'];
    protected $hidden = ['password', 'remember_token'];
}
