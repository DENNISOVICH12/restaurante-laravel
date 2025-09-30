<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToRestaurant;

class Pedido extends Model
{
    use BelongsToRestaurant;

    protected $table = 'pedidos';
    protected $primaryKey = 'id';

    protected $fillable = ['cliente_id', 'restaurant_id', 'estado'];

    protected $appends = ['total', 'fecha'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(\App\Models\Cliente::class, 'cliente_id');
    }

    public function detalle()
    {
        return $this->hasMany(\App\Models\DetallePedido::class, 'pedido_id');
    }

    public function getTotalAttribute(): float
    {
        if (array_key_exists('importe_total', $this->attributes)) {
            return (float) $this->attributes['importe_total'];
        }

        if ($this->relationLoaded('detalle')) {
            return (float) $this->detalle->sum(function ($item) {
                return (float) $item->importe;
            });
        }

        return (float) $this->detalle()->sum('importe');
    }

    public function getFechaAttribute(): ?string
    {
        return $this->created_at ? $this->created_at->format('Y-m-d H:i') : null;
    }
}