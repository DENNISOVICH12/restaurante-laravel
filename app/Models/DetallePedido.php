<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    use HasFactory;
    protected $table = 'pedido_detalles';
    // esta tabla SÍ tiene timestamps según la migración
    public $timestamps = true;

    protected $fillable = [
        'menu_item_id',
        'cantidad',
        'precio_unitario',
        'importe',
        'restaurant_id',
        'pedido_id',
    ];
    

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }
}
