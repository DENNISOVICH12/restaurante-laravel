<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToRestaurant;

class Pedido extends Model
{
    use BelongsToRestaurant;
    protected $table = 'pedidos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    // columnas reales: id_cliente, fecha, estado, mesa
    protected $fillable = [
        'id_cliente',
        'fecha',
        'estado',
        'mesa',
    ];

    public function cliente()
    {
        return $this->belongsTo(\App\Models\Cliente::class, 'id_cliente');
    }

    public function detalle()
    {
        return $this->hasMany(\App\Models\DetallePedido::class, 'id_pedido');
    }
}
