<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    protected $table = 'detalle_pedido';
    protected $primaryKey = 'id';
    public $timestamps = false;

    // columnas reales
    protected $fillable = ['id_pedido','nombre_producto','precio','cantidad','categoria','descripcion'];

    public function pedido() { return $this->belongsTo(\App\Models\Pedido::class, 'id_pedido'); }
}
