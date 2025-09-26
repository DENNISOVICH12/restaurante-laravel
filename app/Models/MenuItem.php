<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToRestaurant;

class MenuItem extends Model
{
    use BelongsToRestaurant;
    protected $table = 'menu_items'; // cambia si tu tabla se llama distinto

    protected $fillable = [
        'nombre', 'descripcion', 'categoria', 'precio', 'imagen', 'disponible',
    ];

    protected $casts = [
        'disponible' => 'boolean',
        'precio'     => 'decimal:2',
    ];
}
