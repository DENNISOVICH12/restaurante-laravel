<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    // Nombre REAL de la tabla en tu BD
    protected $table = 'detalle_pedido';

    // Esta tabla no tiene created_at/updated_at
    public $timestamps = false;

    protected $fillable = [
        'id_pedido',
        'nombre_producto',
        'precio',
        'categoria',
        'cantidad',
        'descripcion',
    ];

    // (opcional) relación inversa al pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }
}

