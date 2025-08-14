<?php

namespace Modules\Teams\app\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Modules\Availability\app\Entities\TeamAvailability;

class Team extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'name',
        'description',
        'tenant_id',
    ];

    public function users()
    {
        return $this->hasMany(\Modules\Users\Entities\User::class);
    }

    public function availability()
    {
        return $this->hasMany(TeamAvailability::class);
    }
}
