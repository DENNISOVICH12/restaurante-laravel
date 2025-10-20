<?php
namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToRestaurant
{
    public static function bootBelongsToRestaurant()
    {
        static::addGlobalScope('restaurant', function (Builder $builder) {
            if (app()->bound('current_restaurant_id')) {
                $rid = app('current_restaurant_id');

                if ($rid !== null) {
                    $builder->where(
                        $builder->getModel()->getTable() . '.restaurant_id',
                        $rid
                    );
                }
            }
        });

        static::creating(function ($model) {
            if (app()->bound('current_restaurant_id')) {
                $rid = app('current_restaurant_id');

                if ($rid !== null) {
                    $model->restaurant_id = $rid;
                }
            }
        });
    }
}
