<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\BelongsToRestaurant;

class Menu extends Model
{
    use HasFactory, BelongsToRestaurant;

    protected $fillable = [
        'restaurant_id','nombre','descripcion','vigencia_desde','vigencia_hasta','activo'
    ];

    public function items()
    {   
        return $this->belongsToMany(MenuItem::class)
                    ->withPivot('precio_override','orden')
                    ->withTimestamps();
    }
}
