<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    public static function bootBelongsToTenant()
    {
        static::creating(function (Model $model) {
            if (empty($model->tenant_id) && app()->bound('currentTenant')) {
                $tenant = app()->make('currentTenant');
                if ($tenant) $model->tenant_id = $tenant->id;
            }
        });

        static::addGlobalScope('tenant_id', function (Builder $builder) {
            if (app()->bound('currentTenant')) {
                $tenant = app()->make('currentTenant');
                if ($tenant) {
                    $table = $builder->getModel()->getTable();
                    $builder->where($table.'.tenant_id', $tenant->id);
                }
            }
        });
    }
}
