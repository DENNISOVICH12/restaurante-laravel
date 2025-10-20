<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla asociada.
     */
    protected $table = 'clientes';

    /**
     * Campos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'nombre_cliente',
        'telefono',
        'direccion',
        'fecha_registro'
    ];
        public $timestamps = false;


    /**
     * Relación con el restaurante.
     * Un cliente pertenece a un restaurante.
     */
    public function restaurante()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    /**
     * Relación con pedidos (si existe esa tabla).
     * Un cliente puede tener muchos pedidos.
     */
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'cliente_id');
        return $this->hasMany(\App\Models\Pedido::class);
    }
}
