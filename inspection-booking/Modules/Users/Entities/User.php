<?php

namespace Modules\Users\Entities;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name','email','password','tenant_id','role'
    ];

    protected $hidden = ['password','remember_token'];

    public function tenant()
    {
        return $this->belongsTo(\Modules\Tenants\Entities\Tenant::class, 'tenant_id');
    }
}
