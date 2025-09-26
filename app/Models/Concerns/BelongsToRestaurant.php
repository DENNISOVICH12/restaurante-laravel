<?php
namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToRestaurant
{
    public static function bootBelongsToRestaurant()
    {
        static::addGlobalScope('restaurant', function (Builder $builder) {
            if ($rid = app('current_restaurant_id')) {
                $builder->where($builder->getModel()->getTable().'.restaurant_id', $rid);
            }
        });

        static::creating(function ($model) {
            if ($rid = app('current_restaurant_id')) {
                $model->restaurant_id = $rid;
            }
        });
    }
}
