<?php

namespace Modules\Tenants\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Users\Entities\User;

class Tenant extends Model
{
    protected $table = 'tenants';
    protected $fillable = ['name','domain','meta'];
    protected $casts = ['meta' => 'array'];

    public function users()
    {
        return $this->hasMany(User::class, 'tenant_id');
    }
}
