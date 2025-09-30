<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    protected $table = 'pedido_detalles';
    // esta tabla SÍ tiene timestamps según la migración
    public $timestamps = true;

    protected $fillable = [
        'pedido_id',
        'restaurant_id',
        'menu_item_id',
        'cantidad',
        'precio_unitario',
        'importe',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }
}
