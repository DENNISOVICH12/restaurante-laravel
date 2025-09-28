<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToRestaurant;

class Pedido extends Model
{
    use BelongsToRestaurant;
    protected $table = 'pedidos';
    protected $primaryKey = 'id';

    protected $fillable = ['cliente_id', 'estado'];

    public function cliente()
    {
 return $this->belongsTo(\App\Models\Cliente::class, 'cliente_id'); 
    }
    // columnas reales: id_cliente, fecha, estado, mesa

    public function detalle()
    {
        return $this->hasMany(\App\Models\DetallePedido::class, 'id_pedido');
    }

