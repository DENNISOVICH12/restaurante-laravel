<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plato extends Model
{
    protected $table = 'platos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_pedido',
        'id_detalle',
        'nombre_producto',
        'precio',
        'cantidad',
        'descripcion',
    ];
}
