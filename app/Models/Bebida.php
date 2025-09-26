<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bebida extends Model
{
    protected $table = 'bebidas';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_pedido','id_detalle','nombre_producto','precio','cantidad','descripcion',
    ];
}
