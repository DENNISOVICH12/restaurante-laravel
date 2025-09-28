<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla asociada
     */
    protected $table = 'restaurants';

    /**
     * Campos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'nombre',
        'slug',
    ];

    /**
     * Relación con clientes (un restaurante tiene muchos clientes)
     */
    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'restaurant_id');
    }

    /**
     * Relación con usuarios (si cada usuario pertenece a un restaurante)
     */
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'restaurant_id');
    }

    /**
     * Relación con menú o ítems de comida (si existe esa tabla)
     */
    public function menuItems()
    {
        return $this->hasMany(MenuItem::class, 'restaurant_id');
    }

    /**
     * Relación con pedidos (si cada pedido pertenece a un restaurante)
     */
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'restaurant_id');
    }
}
